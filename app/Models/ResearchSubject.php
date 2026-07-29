<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'department_id',
        'title',
        'description',
        'responsibilities',
        'candidate_profile',
        'keywords',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'subject_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true);
    }
}
