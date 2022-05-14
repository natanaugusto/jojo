<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function create(): View
    {
        return view(view: 'auth.login');
    }

    /**
     * @param LoginRequest $request
     * @return RedirectResponse|Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse | Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $request->expectsJson() ?
            response()->noContent() : redirect()->intended(default: RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function destroy(Request $request): RedirectResponse|Response
    {
        Auth::guard(name: 'web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->expectsJson() ? response()->noContent() : redirect(to: '/');
    }
}
