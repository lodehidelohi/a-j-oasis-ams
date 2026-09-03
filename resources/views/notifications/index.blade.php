@extends(auth()->user()->isAdmin() || auth()->user()->isStaff() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Notifications')

@section('content')
<x-page-header title="Notifications" />

<div class="list-group shadow-sm">
    @forelse ($notifications as $notification)
        <div class="list-group-item {{ $notification->isRead() ? '' : 'bg-primary-subtle' }} py-3">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1 h6">
                    @unless ($notification->isRead())
                        <span class="badge rounded-pill bg-primary me-1" style="width:.5rem;height:.5rem;padding:0;"></span>
                    @endunless
                    {{ $notification->title }}
                </h5>
                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
            </div>
            <p class="mb-2 text-muted">{{ $notification->message }}</p>
            @unless ($notification->isRead())
                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Mark as read</button>
                </form>
            @endunless
        </div>
    @empty
        <div class="list-group-item">
            <x-empty-state icon="bi-bell" message="No notifications yet." />
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
