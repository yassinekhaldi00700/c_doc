<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Application $application): bool
    {
        return $user->isAdmin()
            || $application->candidate_id === $user->id
            || $application->subject->professor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isCandidate();
    }

    public function review(User $user, Application $application): bool
    {
        return $user->isAdmin();
    }
}
