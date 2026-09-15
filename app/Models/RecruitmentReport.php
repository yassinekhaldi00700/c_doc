<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentReport extends Model
{
    protected $fillable = ['subject_id', 'data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }
}
