<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * Blocks access to the "apply" flow until the candidate has completed
     * their profile (personal info, academic background, required documents).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->profile;

        if (! $profile || ! $profile->isComplete()) {
            // Kept in the session (not flashed) so it survives the multi-step
            // profile wizard, not just the next request.
            if ($subject = $request->route('subject')) {
                $request->session()->put('intended_subject', $subject->id);
            }

            return redirect()->route('candidate.profile.overview')
                ->with('warning', 'Please complete your profile before applying to a subject.');
        }

        return $next($request);
    }
}
