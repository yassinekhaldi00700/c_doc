<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Not ShouldQueue — dispatched inside App\Jobs\SendApplicationNotification,
 * which is what's actually queued, so it can record whether the send
 * succeeded or failed against the application (see decideAndNotify()).
 */
class OralExamInvitation extends Mailable
{
    use SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve been invited to the oral exam — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.oral-exam-invitation',
            with: [
                'candidateName' => $this->application->fullName(),
                'subjectTitle' => $this->application->subject->title,
                'examDate' => $this->application->oral_exam_at?->format('l, F j, Y \a\t g:i A'),
                'applicationUrl' => route('candidate.applications.show', $this->application),
            ],
        );
    }
}
