<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#005292; padding:24px 32px;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">{{ config('app.name') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px; font-size:16px;">Dear {{ $candidateName }},</p>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                Thank you for your interest in <strong>{{ $subjectTitle }}</strong> and for taking the time to apply.
                                After careful review, we regret to inform you that we are unable to offer you a place on this program at this time.
                            </p>
                            @if ($reviewComment)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb; border-radius:6px; margin:0 0 16px;">
                                    <tr>
                                        <td style="padding:14px 18px; font-size:14px; color:#374151;">
                                            <strong>Reviewer's note:</strong> {{ $reviewComment }}
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                We encourage you to apply again for future opportunities. You can review your application at any time from your candidate dashboard.
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                                <tr>
                                    <td style="background-color:#005292; border-radius:6px;">
                                        <a href="{{ $applicationUrl }}" style="display:inline-block; padding:12px 24px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold;">View My Application</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0; font-size:14px; color:#6b7280;">We wish you the very best in your future endeavors.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; background-color:#f9fafb; font-size:12px; color:#9ca3af;">
                            {{ config('app.name') }} &mdash; Euromed University of Fès
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
