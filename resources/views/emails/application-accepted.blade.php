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
                            <p style="margin:0 0 16px; font-size:16px;">Dear {{ $candidateName }},</p>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                We are pleased to inform you that, following the oral interview phase of the admission process to the
                                Doctoral Cycle at the Euro-Mediterranean University of Fes, your application for the research theme entitled
                                &ldquo;<strong>{{ $subjectTitle }}</strong>&rdquo; has been <strong style="color:#44A66D;">accepted</strong>.
                            </p>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                In this regard, we kindly ask you to attend the University's Doctoral Studies Center (CEDoc) (Room 4.69,
                                Building 3)
                                @if ($startDate)
                                    no later than <strong>{{ $startDate }}</strong>,
                                @endif
                                with your complete application file, including:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb; border-radius:6px; margin:0 0 16px;">
                                <tr>
                                    <td style="padding:16px 20px; font-size:15px; color:#1f2937; line-height:1.7;">
                                        &bull; Baccalaureate certificate (original and one certified copy)<br>
                                        &bull; National identity card or passport for foreign candidates (two certified copies)<br>
                                        &bull; Bachelor's, Master's, or Engineering degree certificate (original and one certified copy)<br>
                                        &bull; All corresponding academic transcripts (original and one certified copy)<br>
                                        &bull; Curriculum Vitae (CV) with a recent photograph<br>
                                        &bull; Four (4) recent passport-size photographs<br>
                                        &bull; Bank account details (RIB)
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px; font-size:16px; line-height:1.5;">
                                Furthermore, candidates holding a foreign degree will be granted a period of three (3) months from the date
                                of registration to submit the official diploma equivalency certificate. After this deadline, their
                                registration cannot be maintained.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                                <tr>
                                    <td style="background-color:#005292; border-radius:6px;">
                                        <a href="{{ $applicationUrl }}" style="display:inline-block; padding:12px 24px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold;">View My Application</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:16px;">Kind regards,</p>
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
