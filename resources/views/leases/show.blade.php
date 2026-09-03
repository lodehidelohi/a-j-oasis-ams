@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Lease Details')

@section('content')
<x-page-header :title="'Lease #' . $lease->id" />

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <dl class="row mb-0">
            <dt class="col-sm-3 text-muted fw-normal">Tenant</dt>
            <dd class="col-sm-9">{{ $lease->tenant->name }}</dd>
            <dt class="col-sm-3 text-muted fw-normal">Room</dt>
            <dd class="col-sm-9">{{ $lease->room->room_number }} ({{ $lease->room->property->name }})</dd>
            <dt class="col-sm-3 text-muted fw-normal">Start Date</dt>
            <dd class="col-sm-9">{{ $lease->start_date->format('M d, Y') }}</dd>
            <dt class="col-sm-3 text-muted fw-normal">Status</dt>
            <dd class="col-sm-9"><x-status-badge :status="$lease->status" /></dd>
        </dl>

        @if (auth()->user()->isAdmin())
            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                <form method="POST" action="{{ route('admin.leases.generate-rent', $lease) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-cash-coin me-1"></i>Generate Next Month's Rent</button>
                </form>
                <a href="{{ route('admin.utility-bills.create', $lease) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-lightning-charge me-1"></i>Encode Utility Bill</a>
            </div>
        @else
            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                <a href="{{ route('tenant.maintenance-requests.create', $lease) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-tools me-1"></i>Report Maintenance Issue</a>
                <a href="{{ route('tenant.room-transfers.create', $lease) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left-right me-1"></i>Request Room Transfer</a>
                <a href="{{ route('tenant.move-outs.create', $lease) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-left me-1"></i>Request Move-Out</a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="bi bi-credit-card me-1"></i>Payments</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Type</th><th>Amount</th><th>Due</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($lease->payments as $payment)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->due_date->format('M d, Y') }}</td>
                            <td><x-status-badge :status="$payment->status" /></td>
                            <td>
                                @if (! auth()->user()->isAdmin() && $payment->status === 'pending')
                                    <form method="POST" action="{{ route('payments.pay', $payment) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success"><i class="bi bi-credit-card me-1"></i>Pay via Xendit</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="bi-credit-card" message="No payments yet." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="bi bi-lightning-charge me-1"></i>Utility Bills</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Type</th><th>Amount</th><th>Due</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($lease->utilityBills as $bill)
                        <tr>
                            <td>{{ ucfirst($bill->type) }}</td>
                            <td>₱{{ number_format($bill->amount, 2) }}</td>
                            <td>{{ $bill->due_date->format('M d, Y') }}</td>
                            <td><x-status-badge :status="$bill->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty-state icon="bi-lightning-charge" message="No utility bills yet." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="bi bi-tools me-1"></i>Maintenance Requests</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Category</th><th>Description</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($lease->maintenanceRequests as $request)
                        <tr>
                            <td>{{ $request->category }}</td>
                            <td>{{ $request->description }}</td>
                            <td><x-status-badge :status="$request->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><x-empty-state icon="bi-tools" message="No maintenance requests yet." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3"><i class="bi bi-arrow-left-right me-1"></i>Room Transfers</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>From</th><th>To</th><th>Deposit Adjustment</th><th>Status</th>@if (auth()->user()->isAdmin())<th></th>@endif</tr>
                </thead>
                <tbody>
                    @forelse ($lease->roomTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->fromRoom->room_number }}</td>
                            <td>{{ $transfer->toRoom->room_number }}</td>
                            <td>₱{{ number_format($transfer->deposit_adjustment, 2) }}</td>
                            <td><x-status-badge :status="$transfer->status" /></td>
                            @if (auth()->user()->isAdmin())
                                <td>
                                    @if ($transfer->status === 'pending')
                                        <form method="POST" action="{{ route('admin.room-transfers.approve', $transfer) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.room-transfers.reject', $transfer) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="bi-arrow-left-right" message="No room transfers yet." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($lease->moveOut)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3"><i class="bi bi-box-arrow-left me-1"></i>Move-Out</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted fw-normal">Requested Date</dt>
                <dd class="col-sm-8">{{ $lease->moveOut->requested_move_out_date->format('M d, Y') }}</dd>
                <dt class="col-sm-4 text-muted fw-normal">Calculated Refund</dt>
                <dd class="col-sm-8 fw-semibold">₱{{ number_format($lease->moveOut->refund_amount, 2) }}</dd>
                <dt class="col-sm-4 text-muted fw-normal">Refund Status</dt>
                <dd class="col-sm-8"><x-status-badge :status="$lease->moveOut->refund_status" /></dd>
            </dl>
            @if (auth()->user()->isAdmin() && $lease->moveOut->refund_status === 'calculated')
                <form method="POST" action="{{ route('admin.move-outs.complete', $lease->moveOut) }}" class="mt-3 pt-3 border-top">
                    @csrf
                    <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg me-1"></i>Mark Refund as Disbursed &amp; Close Lease</button>
                </form>
            @endif
        </div>
    </div>
@endif
@endsection
