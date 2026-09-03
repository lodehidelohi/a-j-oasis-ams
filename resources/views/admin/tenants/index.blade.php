@extends('layouts.admin')

@section('title', 'Tenants')

@section('content')
<x-page-header title="Tenants" subtitle="All tenant accounts and their current room">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Tenant</a>
    </x-slot:actions>
</x-page-header>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Current Room</th><th>Account Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($tenants as $tenant)
                    @php($lease = $tenant->leases->first())
                    <tr>
                        <td class="fw-medium">{{ $tenant->name }}</td>
                        <td>{{ $tenant->email }}</td>
                        <td>{{ $tenant->phone ?? '—' }}</td>
                        <td>
                            @if ($lease)
                                Room {{ $lease->room->room_number }}
                            @else
                                <span class="text-muted">No active lease</span>
                            @endif
                        </td>
                        <td><x-status-badge :status="$tenant->is_active ? 'active' : 'deactivated'" /></td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @if ($lease)
                                <a href="{{ route('admin.leases.show', $lease) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-text"></i></a>
                            @endif
                            <form method="POST" action="{{ route('admin.users.toggle-active', $tenant) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm {{ $tenant->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    <i class="bi {{ $tenant->is_active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="bi-people" message="No tenants found." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $tenants->links() }}</div>
@endsection
