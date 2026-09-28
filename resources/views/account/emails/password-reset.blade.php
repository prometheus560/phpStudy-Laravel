<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>

<body style="margin:0; padding:30px; background:whitesmoke; font-family:Arial, sans-serif;">

    <div style="max-width:600px; margin:auto; background:white; border-radius:12px; overflow:hidden;">

        <div style="background:slateblue; color:white; padding:30px; text-align:center;">
            <h1 style="margin:0;">
                Study Planner
            </h1>

            <p style="margin-bottom:0;">
                Password Reset
            </p>
        </div>

        <div style="padding:35px;">

            <h2>
                Hello, {{ $user->name }}
            </h2>

            <p style="color:gray; line-height:1.6;">
                We received a request to reset your Study Planner password.
                Click the button below to create a new password.
            </p>

            <div style="text-align:center; margin:30px 0;">

                <a
                    href="{{ $resetUrl }}"
                    style="
                        display:inline-block;
                        background:slateblue;
                        color:white;
                        text-decoration:none;
                        padding:14px 28px;
                        border-radius:8px;
                        font-weight:bold;
                    "
                >
                    Reset Password
                </a>

            </div>

            <p style="color:gray; line-height:1.6;">
                This password reset link will expire in 30 minutes.
            </p>

            <p style="color:gray; line-height:1.6;">
                If you did not request a password reset,
                you can safely ignore this email.
            </p>

            <hr style="border:0; border-top:1px solid lightgray; margin:30px 0;">

            <p style="color:gray; font-size:13px; text-align:center;">
                Study Planner · Stay organized · Study smarter
            </p>

        </div>

    </div>

</body>

</html>
