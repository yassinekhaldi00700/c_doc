<?php

namespace App\Policies;

use App\Models\ApplicationDocument;
use App\Models\User;

class ApplicationDocumentPolicy
{
    public function view(User $user, ApplicationDocument $document): bool
    {
        $application = $document->application;

        return $user->isAdmin()
            || $application->candidate_id === $user->id
            || $application->subject->professor_id === $user->id;
    }
}
