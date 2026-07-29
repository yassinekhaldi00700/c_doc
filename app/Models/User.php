<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Mail\VerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The candidate's reusable application profile (personal info, academic
     * background, and standard documents), shared across every subject
     * application they submit.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Research subjects owned by this user, when acting as a professor.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(ResearchSubject::class, 'professor_id');
    }

    /**
     * Applications submitted by this user, when acting as a candidate.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function reviewedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'reviewed_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isProfessor(): bool
    {
        return $this->role === UserRole::Professor;
    }

    public function isCandidate(): bool
    {
        return $this->role === UserRole::Candidate;
    }

    /**
     * True until the user has changed their password at least once —
     * e.g. a professor account created by an admin, still on the
     * admin-assigned password.
     */
    public function needsPasswordChange(): bool
    {
        return is_null($this->password_changed_at);
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            UserRole::Admin => 'admin.dashboard',
            UserRole::Professor => 'professor.dashboard',
            UserRole::Candidate => 'candidate.dashboard',
        };
    }

    /**
     * Overrides the trait's default (Laravel-branded) verification email
     * with App\Mail\VerifyEmail, styled to match the rest of the app's
     * notification emails.
     */
    public function sendEmailVerificationNotification(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        Mail::to($this->email)->send(new VerifyEmail($this, $verificationUrl));
    }
}
