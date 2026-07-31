<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            // A transient network/API failure here shouldn't crash the page
            // with a raw stack trace (or a blank 500 in production) — let
            // the candidate know to try again instead.
            Log::error('Failed to send verification email: '.$e->getMessage(), ['user_id' => $request->user()->id]);

            return back()->with('error', 'We couldn\'t send the verification email right now. Please try again in a moment.');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
