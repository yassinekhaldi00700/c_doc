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
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">Doctoral Cycle at the Euro-Mediterranean University of Fes</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px; font-size:16px;">Dear {{ $userName }},</p>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                Thanks for creating an account. Please confirm this is your email address by clicking the button below.
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                                <tr>
                                    <td style="background-color:#005292; border-radius:6px;">
                                        <a href="{{ $verificationUrl }}" style="display:inline-block; padding:12px 24px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold;">Verify Email Address</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 8px; font-size:14px; color:#6b7280;">
                                If you did not create an account, no further action is required.
                            </p>
                            <p style="margin:16px 0 0; font-size:13px; color:#9ca3af; word-break:break-all;">
                                If the button above doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ $verificationUrl }}" style="color:#005292;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; background-color:#f9fafb; font-size:12px; color:#9ca3af;">
                             Euromed University of Fès
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
