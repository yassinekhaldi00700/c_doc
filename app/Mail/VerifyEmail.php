<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Replaces Laravel's default (unbranded, "Laravel"-labeled) verification
 * email — sent from App\Models\User::sendEmailVerificationNotification().
 */
class VerifyEmail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user, public string $verificationUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your email address — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
            with: [
                'userName' => $this->user->name,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
