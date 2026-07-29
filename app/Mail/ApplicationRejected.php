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
class ApplicationRejected extends Mailable
{
    use SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on your application — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-rejected',
            with: [
                'candidateName' => $this->application->fullName(),
                'subjectTitle' => $this->application->subject->title,
                'reviewComment' => $this->application->review_comment,
                'applicationUrl' => route('candidate.applications.show', $this->application),
            ],
        );
    }
}
