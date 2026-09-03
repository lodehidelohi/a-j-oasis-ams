<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = User::where('role', 'tenant')
            ->with(['leases' => fn ($q) => $q->where('status', 'active')->with('room')])
            ->orderBy('name')
            ->paginate(25);

        return view('admin.tenants.index', compact('tenants'));
    }
}
