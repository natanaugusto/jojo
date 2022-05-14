<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return View
     */
    public function create(): View
    {
        return view(view: 'auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param Request $request
     * @return RedirectResponse|Response|JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse|Response|JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only(keys: 'email')
        );
        if ($request->expectsJson()) {
            return $status == Password::RESET_LINK_SENT
                ? response()->noContent() : response()->json(
                    data: ['errors' => ['email' => __($status)]],
                    status: SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY
                );
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only(keys: 'email'))
                            ->withErrors(['email' => __($status)]);
    }
}
