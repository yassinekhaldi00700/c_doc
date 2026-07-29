<?php

namespace App\Enums;

enum UserRole: int
{
    case Admin = 1;
    case Professor = 2;
    case Candidate = 3;

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Professor => 'Professor',
            self::Candidate => 'Candidate',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-dark',
            self::Professor => 'bg-primary',
            self::Candidate => 'bg-success',
        };
    }

    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'admin', 'administrator' => self::Admin,
            'professor' => self::Professor,
            'candidate' => self::Candidate,
            default => null,
        };
    }
}
