<?php

namespace App\Http\Middleware;

use App\Models\AdmissionSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationsAreAccepting
{
    /**
     * Keep the application form and submission endpoint closed while the
     * global admin pause is active. Subjects themselves remain visible.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (AdmissionSetting::applicationsArePaused()) {
            return redirect()->route('candidate.subjects.show', $request->route('subject'))
                ->with('warning', 'Applications are temporarily paused. You can still browse research subjects, but no application can be submitted right now.');
        }

        return $next($request);
    }
}
