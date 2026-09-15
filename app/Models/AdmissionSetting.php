<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionSetting extends Model
{
    protected $fillable = [
        'applications_paused',
        'paused_at',
        'paused_by',
    ];

    protected function casts(): array
    {
        return [
            'applications_paused' => 'boolean',
            'paused_at' => 'datetime',
        ];
    }

    public function pausedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paused_by');
    }

    public static function applicationsArePaused(): bool
    {
        return (bool) static::query()->value('applications_paused');
    }
}
