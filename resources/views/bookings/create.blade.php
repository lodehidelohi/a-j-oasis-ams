@extends('layouts.app')

@section('title', 'Book Room')

@section('content')
<x-page-header :title="'Book Room ' . $room->room_number">
    <x-slot:actions>
        <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>View Full Details</a>
    </x-slot:actions>
</x-page-header>

<div class="card border-0 shadow-sm" style="max-width: 560px;">
    <div class="card-body p-4">
        <h2 class="h6 text-muted mb-3">{{ $room->property->name }} &middot; Floor {{ $room->floor }} &middot; {{ ucfirst($room->type) }}</h2>

        <dl class="row mb-3">
            <dt class="col-6 fw-normal text-muted">Monthly rate</dt>
            <dd class="col-6 text-end">₱{{ number_format($room->monthly_rate, 2) }}</dd>
            <dt class="col-6 fw-normal text-muted">Upfront payment (advance + deposit + security)</dt>
            <dd class="col-6 text-end fw-semibold">₱{{ number_format($room->monthly_rate * 3, 2) }}</dd>
        </dl>

        <div class="alert alert-info d-flex align-items-start small">
            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
            <div>You'll have 7 days from booking to confirm your move-in date. You can also select it now.</div>
        </div>

        <form method="POST" action="{{ route('tenant.bookings.store', $room) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Move-in Date (optional now)</label>
                <input type="date" name="move_in_date" class="form-control" min="{{ now()->toDateString() }}">
            </div>
            <button class="btn btn-primary w-100"><i class="bi bi-credit-card me-1"></i>Confirm Booking &amp; Pay via Xendit</button>
        </form>
    </div>
</div>
@endsection
