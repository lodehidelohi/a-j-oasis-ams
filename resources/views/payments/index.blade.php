@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Payments')

@section('content')
<x-page-header :title="auth()->user()->isAdmin() ? 'All Payments' : 'My Payments'" />

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Type</th><th>Reference</th><th>Amount</th><th>Due</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>
                        <td class="text-muted">
                            @if ($payment->lease) Lease #{{ $payment->lease_id }} @elseif ($payment->booking) Booking #{{ $payment->booking_id }} @endif
                        </td>
                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->due_date->format('M d, Y') }}</td>
                        <td><x-status-badge :status="$payment->status" /></td>
                        <td class="text-end">
                            @if (! auth()->user()->isAdmin() && $payment->status === 'pending')
                                <form method="POST" action="{{ route('payments.pay', $payment) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success"><i class="bi bi-credit-card me-1"></i>Pay via Xendit</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="bi-credit-card" message="No payments yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $payments->links() }}</div>
@endsection
