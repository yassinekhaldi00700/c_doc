<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sends mail through Microsoft Graph's /sendMail endpoint (OAuth2 client-
 * credentials flow) instead of SMTP — avoids the "Authenticated SMTP"
 * restriction Microsoft 365 tenants increasingly disable by default.
 */
class MicrosoftGraphTransport extends AbstractTransport
{
    public function __construct(
        protected string $tenantId,
        protected string $clientId,
        protected string $clientSecret,
        protected string $sender,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $response = Http::withToken($this->accessToken())
            ->post("https://graph.microsoft.com/v1.0/users/{$this->sender}/sendMail", [
                'message' => $this->toGraphMessage($email),
                'saveToSentItems' => true,
            ]);

        if ($response->failed()) {
            throw new TransportException(
                'Microsoft Graph sendMail failed ('.$response->status().'): '.$response->body()
            );
        }
    }

    protected function accessToken(): string
    {
        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Could not obtain a Microsoft Graph access token ('.$response->status().'): '.$response->body()
            );
        }

        $token = $response->json('access_token');

        if (! $token) {
            throw new RuntimeException('Microsoft Graph token response did not include an access_token.');
        }

        return $token;
    }

    /**
     * @return array<string, mixed>
     */
    protected function toGraphMessage(Email $email): array
    {
        $body = $email->getHtmlBody() ?? $email->getTextBody() ?? '';

        return [
            'subject' => (string) $email->getSubject(),
            'body' => [
                'contentType' => $email->getHtmlBody() ? 'HTML' : 'Text',
                'content' => $body,
            ],
            'toRecipients' => $this->recipients($email->getTo()),
            'ccRecipients' => $this->recipients($email->getCc()),
            'bccRecipients' => $this->recipients($email->getBcc()),
            'replyTo' => $this->recipients($email->getReplyTo()),
        ];
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return array<int, array<string, mixed>>
     */
    protected function recipients(array $addresses): array
    {
        return collect($addresses)
            ->map(fn (Address $address) => [
                'emailAddress' => array_filter([
                    'address' => $address->getAddress(),
                    'name' => $address->getName() ?: null,
                ]),
            ])
            ->all();
    }

    public function __toString(): string
    {
        return 'graph';
    }
}
