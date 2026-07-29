<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Mail\ApplicationAccepted;
use App\Mail\ApplicationRejected;
use App\Mail\OralExamInvitation;
use App\Models\Application;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ApplicationDecisionService
{
    public function decide(
        Application $application,
        ApplicationStatus $status,
        User $reviewer,
        ?string $comment = null,
        ?string $oralExamDate = null,
        ?string $programStartDate = null,
    ): Application {
        $previousStatus = $application->status;

        $updated = DB::transaction(function () use ($application, $status, $reviewer, $comment, $previousStatus, $oralExamDate, $programStartDate) {
            $attributes = [
                'status' => $status,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_comment' => $comment,
            ];

            // Only touched when (re-)setting the matching status, so an
            // already-recorded date isn't wiped out by a later, unrelated
            // status change.
            if ($status === ApplicationStatus::UnderReview) {
                $attributes['oral_exam_at'] = $oralExamDate;
            }

            if ($status === ApplicationStatus::Accepted) {
                $attributes['program_start_at'] = $programStartDate;
            }

            // A fresh decision means a fresh notification attempt — clear
            // any leftover sent/failed state from a previous status so the
            // admin isn't looking at stale delivery info for this one.
            if ($previousStatus !== $status) {
                $attributes['notification_sent_at'] = null;
                $attributes['notification_error'] = null;
            }

            $application->update($attributes);

            $application->statusLogs()->create([
                'from_status' => $previousStatus,
                'to_status' => $status,
                'changed_by' => $reviewer->id,
                'comment' => $comment,
            ]);

            return $application->fresh();
        });

        // Only notify on an actual transition into this status, so
        // re-saving a comment on an already-decided application doesn't
        // spam the candidate with a repeat email. Sent synchronously (not
        // queued) — this is an occasional admin action, not bulk mail, so
        // the admin sees whether it actually went out immediately instead
        // of needing a queue worker running in the background.
        if ($previousStatus !== $status) {
            $this->notifyCandidate($updated);
        }

        return $updated;
    }

    /**
     * Retry the decision email for the application's current status —
     * used by the admin's "Resend" button after a failed send.
     */
    public function resendNotification(Application $application): void
    {
        $this->notifyCandidate($application);
    }

    private function notifyCandidate(Application $application): void
    {
        $application->loadMissing(['candidate.profile', 'subject']);

        $mailable = $this->mailableFor($application);

        if (! $mailable) {
            return;
        }

        try {
            Mail::to($application->candidate->email)->send($mailable);

            $application->update([
                'notification_sent_at' => now(),
                'notification_error' => null,
            ]);
        } catch (Throwable $e) {
            $application->update([
                'notification_sent_at' => null,
                'notification_error' => $e->getMessage(),
            ]);
        }
    }

    private function mailableFor(Application $application): ?Mailable
    {
        return match ($application->status) {
            ApplicationStatus::UnderReview => new OralExamInvitation($application),
            ApplicationStatus::Accepted => new ApplicationAccepted($application),
            ApplicationStatus::Rejected => new ApplicationRejected($application),
            default => null,
        };
    }
}
