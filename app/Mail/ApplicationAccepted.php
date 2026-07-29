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
class ApplicationAccepted extends Mailable
{
    use SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations — your application has been accepted — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-accepted',
            with: [
                'candidateName' => $this->application->fullName(),
                'subjectTitle' => $this->application->subject->title,
                'startDate' => $this->application->program_start_at?->format('l, F j, Y'),
                'applicationUrl' => route('candidate.applications.show', $this->application),
            ],
        );
    }
}
