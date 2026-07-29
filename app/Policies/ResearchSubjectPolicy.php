<?php

namespace App\Policies;

use App\Models\ResearchSubject;
use App\Models\User;

class ResearchSubjectPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ResearchSubject $subject): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ResearchSubject $subject): bool
    {
        return $user->isAdmin() || ($user->isProfessor() && $subject->professor_id === $user->id);
    }

    public function delete(User $user, ResearchSubject $subject): bool
    {
        return $user->isAdmin() || ($user->isProfessor() && $subject->professor_id === $user->id);
    }
}
