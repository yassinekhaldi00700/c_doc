<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Public self-registration always creates a Candidate account.
     * Professor and Admin accounts are provisioned by an administrator.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'privacy_consent' => ['required', 'accepted'],
        ], [
            'privacy_consent.required' => 'You must consent to the processing of your personal data before registering.',
            'privacy_consent.accepted' => 'You must consent to the processing of your personal data before registering.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => UserRole::Candidate,
        ]);

        // Firing this normally sends the verification email inline (a real
        // Graph API call — a couple of seconds, sometimes much more), which
        // would make the registration form hang. Deferring it until after
        // the response is sent keeps signup fast without needing a queue
        // worker running. Wrapped in its own try/catch since this runs
        // after the response has already gone out — an unhandled failure
        // here (e.g. a network blip reaching Microsoft Graph) has no
        // request left to report an error on, so the only thing to do is
        // log it; the candidate can still request a new link from the
        // "verify email" prompt regardless.
        dispatch(function () use ($user) {
            try {
                event(new Registered($user));
            } catch (Throwable $e) {
                Log::error('Failed to send verification email on registration: '.$e->getMessage(), ['user_id' => $user->id]);
            }
        })->afterResponse();

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
