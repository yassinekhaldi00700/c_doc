<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Accepted for Oral Exam',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-secondary',
            self::UnderReview => 'bg-primary',
            self::Accepted => 'bg-success',
            self::Rejected => 'bg-danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'bi-hourglass-split',
            self::UnderReview => 'bi-search',
            self::Accepted => 'bi-check-circle-fill',
            self::Rejected => 'bi-x-circle-fill',
        };
    }
}
