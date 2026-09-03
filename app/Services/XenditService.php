<?php

namespace App\Services;

use App\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;

class XenditService
{
    protected InvoiceApi $invoiceApi;

    public function __construct()
    {
        Configuration::setXenditKey((string) config('xendit.secret_key'));

        $this->invoiceApi = new InvoiceApi;
    }

    /**
     * Create a Xendit invoice.
     *
     * @param  string  $externalId  Unique reference, e.g. "booking-123"
     * @param  float  $amount
     * @param  string  $description
     * @param  string|null  $payerEmail
     * @return array{invoice_id: string, invoice_url: string, status: string}
     *
     * @throws PaymentGatewayException
     */
    public function createInvoice(string $externalId, float $amount, string $description, ?string $payerEmail = null): array
    {
        if (config('xendit.fake_mode')) {
            $fakeId = 'fake_'.Str::uuid();

            return [
                'invoice_id' => $fakeId,
                'invoice_url' => route('payments.fake-complete', $fakeId),
                'status' => 'PENDING',
            ];
        }

        $request = new CreateInvoiceRequest([
            'external_id' => $externalId,
            'amount' => $amount,
            'description' => $description,
            'currency' => 'PHP',
            'invoice_duration' => 86400,
            'payer_email' => $payerEmail,
            'success_redirect_url' => route('payments.success'),
            'failure_redirect_url' => route('payments.failure'),
        ]);

        try {
            $invoice = $this->invoiceApi->createInvoice($request);
        } catch (XenditSdkException $e) {
            Log::error('Xendit createInvoice failed', [
                'external_id' => $externalId,
                'message' => $e->getMessage(),
                'full_error' => $e->getFullError(),
            ]);

            throw new PaymentGatewayException(
                'The payment gateway is temporarily unavailable. Please try again later.',
                previous: $e,
            );
        }

        return [
            'invoice_id' => $invoice['id'],
            'invoice_url' => $invoice['invoice_url'],
            'status' => $invoice['status'],
        ];
    }

    /**
     * @throws PaymentGatewayException
     */
    public function getInvoice(string $invoiceId): array
    {
        if (config('xendit.fake_mode') && str_starts_with($invoiceId, 'fake_')) {
            return [
                'invoice_url' => route('payments.fake-complete', $invoiceId),
                'status' => 'PENDING',
            ];
        }

        try {
            return (array) $this->invoiceApi->getInvoiceById($invoiceId);
        } catch (XenditSdkException $e) {
            Log::error('Xendit getInvoiceById failed', [
                'invoice_id' => $invoiceId,
                'message' => $e->getMessage(),
                'full_error' => $e->getFullError(),
            ]);

            throw new PaymentGatewayException(
                'The payment gateway is temporarily unavailable. Please try again later.',
                previous: $e,
            );
        }
    }
}
