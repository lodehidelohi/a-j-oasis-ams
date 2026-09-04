<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentGatewayException;
use App\Models\Lease;
use App\Models\Payment;
use App\Services\NotificationService;
use App\Services\PaymentCompletionService;
use App\Services\XenditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected XenditService $xendit,
        protected PaymentCompletionService $completion,
        protected NotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $payments = $request->user()->isAdmin()
            ? Payment::with(['lease.tenant', 'booking.tenant'])->latest()->paginate(25)
            : Payment::whereHas('lease', fn ($q) => $q->where('tenant_id', $request->user()->id))
                ->orWhereHas('booking', fn ($q) => $q->where('user_id', $request->user()->id))
                ->with(['lease', 'booking'])
                ->latest()
                ->paginate(25);

        if ($request->user()->isTenant()) {
            $this->notifications->markTypesRead($request->user(), ['payment', 'utility']);
        } elseif ($request->user()->isAdmin()) {
            $this->notifications->markTypesRead($request->user(), ['payment']);
        }

        return view('payments.index', compact('payments'));
    }

    /**
     * Admin manually generates the next rent payment for an active lease.
     */
    public function generateRent(Lease $lease): RedirectResponse
    {
        abort_unless($lease->status === 'active', 422, 'Lease is not active.');

        $dueDate = now()->addMonth()->startOfMonth();

        Payment::create([
            'lease_id' => $lease->id,
            'type' => 'rent',
            'amount' => $lease->room->monthly_rate,
            'due_date' => $dueDate,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Rent payment generated for '.$dueDate->format('F Y').'.');
    }

    /**
     * Tenant initiates payment for a pending payment record via Xendit.
     */
    public function pay(Payment $payment): RedirectResponse
    {
        $tenant = $payment->lease?->tenant ?? $payment->booking?->tenant;

        abort_unless($tenant && $tenant->id === auth()->id(), 403);
        abort_unless($payment->status === 'pending', 422, 'This payment is not payable.');

        try {
            if (! $payment->xendit_invoice_id) {
                $invoice = $this->xendit->createInvoice(
                    externalId: 'payment-'.$payment->id,
                    amount: (float) $payment->amount,
                    description: ucfirst($payment->type).' payment',
                    payerEmail: $tenant->email,
                );

                $payment->update(['xendit_invoice_id' => $invoice['invoice_id']]);

                return redirect()->away($invoice['invoice_url']);
            }

            $invoice = $this->xendit->getInvoice($payment->xendit_invoice_id);

            return redirect()->away($invoice['invoice_url']);
        } catch (PaymentGatewayException $e) {
            return back()->withErrors(['gateway' => $e->getMessage()]);
        }
    }

    /**
     * Manually polls Xendit for this invoice's current status and applies completion
     * if it's PAID. Xendit's webhook can only reach a publicly-hosted app, so while
     * developing against a local server this is the only way payment completion can
     * reach the app — it does the same job the webhook does, just pulled instead of pushed.
     */
    public function checkStatus(Payment $payment): RedirectResponse
    {
        $tenant = $payment->lease?->tenant ?? $payment->booking?->tenant;

        abort_unless($tenant && $tenant->id === auth()->id(), 403);

        if ($payment->status === 'paid') {
            return back()->with('status', 'This payment is already marked as paid.');
        }

        abort_unless($payment->xendit_invoice_id, 422, 'No Xendit invoice has been created for this payment yet.');

        try {
            $invoice = $this->xendit->getInvoice($payment->xendit_invoice_id);
        } catch (PaymentGatewayException $e) {
            return back()->withErrors(['gateway' => $e->getMessage()]);
        }

        if ($invoice['status'] === 'PAID' || $invoice['status'] === 'SETTLED') {
            $this->completion->complete($payment, $invoice['invoice_id']);

            return redirect()->route('payments.success');
        }

        return back()->with('status', 'Not paid yet (status: '.$invoice['status'].'). Complete checkout on Xendit, then check again.');
    }

    /**
     * TEMPORARY test-run route: only reachable when XENDIT_FAKE_MODE is on.
     * Simulates the webhook's "PAID" callback so the flow can be exercised
     * end-to-end without a working Xendit account.
     */
    public function fakeComplete(string $reference): RedirectResponse
    {
        abort_unless(config('xendit.fake_mode'), 404);

        $payment = Payment::where('xendit_invoice_id', $reference)->firstOrFail();
        $tenant = $payment->lease?->tenant ?? $payment->booking?->tenant;

        abort_unless($tenant && $tenant->id === auth()->id(), 403);

        $this->completion->complete($payment, 'fake_'.now()->timestamp);

        return redirect()->route('payments.success');
    }

    public function success(): View
    {
        return view('payments.success');
    }

    public function failure(): View
    {
        return view('payments.failure');
    }
}
