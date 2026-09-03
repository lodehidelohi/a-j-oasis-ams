<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = Announcement::with(['author', 'property'])
            ->visibleTo($request->user())
            ->latest()
            ->paginate(20);

        return view('announcements.index', compact('announcements'));
    }
}
