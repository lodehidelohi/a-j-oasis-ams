@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Maintenance Requests')

@section('content')
<x-page-header title="Maintenance Requests" />

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @if (! auth()->user()->isTenant())<th>Tenant</th>@endif
                    <th>Room</th><th>Category</th><th>Description</th><th>Status</th>
                    @if (! auth()->user()->isTenant())<th>Assigned To</th><th>Update</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        @if (! auth()->user()->isTenant())<td>{{ $request->tenant->name }}</td>@endif
                        <td>{{ $request->room->room_number }}</td>
                        <td>{{ $request->category }}</td>
                        <td>{{ $request->description }}</td>
                        <td><x-status-badge :status="$request->status" /></td>
                        @if (! auth()->user()->isTenant())
                            <td>{{ $request->assignee?->name ?? '—' }}</td>
                            <td>
                                <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.maintenance-requests.update', $request) : route('staff.maintenance.update', $request) }}" class="d-flex gap-1">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach (['pending', 'in_progress', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($request->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="bi-tools" message="No maintenance requests yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $requests->links() }}</div>
@endsection
