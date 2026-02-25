<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f1d8e2; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 40px auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .logo { max-width: 180px; margin-bottom: 20px; display: block; }
        .banner { background: linear-gradient(135deg, #9A2E4A 0%, #b8325d 100%); padding: 24px; border-radius: 12px; margin-bottom: 24px; color: #ffffff; }
        .banner h2 { margin: 0 0 8px; font-size: 22px; }
        .banner p { margin: 0; opacity: 0.95; }
        .reset-button { display: inline-block; background: #b8325d; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-top: 20px; }
        .reset-button:hover { background: #9A2E4A; }
        .footer { margin-top: 24px; font-size: 14px; color: #6B7280; text-align: center; }
        .footer img { max-width: 140px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="email-container" style="text-align: center;">
        <img src="{{ (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png' }}" alt="{{ config('app.name') }} logo" class="logo" style="margin-left: auto; margin-right: auto;">
        <div class="banner" style="text-align: left;">
            <h2>Password Reset Request</h2>
            <p>Dear Admin, you have requested to reset your password. Click the button below to proceed.</p>
        </div>
        <p style="color: #374151;">Click the button below to choose a new password:</p>
        <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
        <p style="margin-top: 20px; color: #6B7280;">If you did not request this, please ignore this email.</p>
        <p class="footer">Regards, <br> <strong style="color: #b8325d;">{{ config('app.name') }}</strong></p>
        <img src="{{ (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png' }}" alt="{{ config('app.name') }} logo" class="footer">
    </div>
</body>
</html>
