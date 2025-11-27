<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = Auth::user()->role; // <-- PASTI BERFUNGSI

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'penatua' => redirect()->route('penatua.dashboard'),
            'koordinator_bidang' => redirect()->route('koordinator.dashboard'),
            'sekretaris' => redirect()->route('sekretaris.dashboard'),
            'pekerja' => redirect()->route('pekerja.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
