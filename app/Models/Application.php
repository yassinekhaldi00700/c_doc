<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'subject_id',
        'status',
        'motivation_summary',
        'reviewed_by',
        'reviewed_at',
        'review_comment',
        'professor_favorited_at',
        'professor_proposed_exam_at',
        'notification_sent_at',
        'notification_error',
        'oral_exam_at',
        'program_start_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'reviewed_at' => 'datetime',
            'professor_favorited_at' => 'datetime',
            'professor_proposed_exam_at' => 'date',
            'notification_sent_at' => 'datetime',
            'oral_exam_at' => 'datetime',
            'program_start_at' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * True once a decision (oral exam / accepted / rejected) has been made
     * but its notification email hasn't resolved to sent or failed yet —
     * i.e. it's still sitting in the queue.
     */
    public function notificationPending(): bool
    {
        return in_array($this->status, [ApplicationStatus::UnderReview, ApplicationStatus::Accepted, ApplicationStatus::Rejected], true)
            && ! $this->notification_sent_at
            && ! $this->notification_error;
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ResearchSubject::class, 'subject_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class)->latest();
    }

    public function fullName(): string
    {
        return $this->candidate->profile?->fullName() ?: $this->candidate->name;
    }
}
