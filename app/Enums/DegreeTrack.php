<?php

namespace App\Enums;

enum DegreeTrack: string
{
    case Master = 'master';
    case Engineer = 'engineer';

    public function label(): string
    {
        return match ($this) {
            self::Master => 'Master',
            self::Engineer => 'Engineering Degree',
        };
    }
}
