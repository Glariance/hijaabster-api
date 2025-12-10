<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* text-align: center; */
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
        }
        .reset-button {
            display: inline-block;
            background: #007bff;
            color: #ffffff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }
        .reset-button:hover {
            background: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/1/1e/D.E.M.O._Logo_2006.svg" alt="Logo" class="logo"> 
        <h2>Password Reset Request</h2>
        <p>Dear Admin,</p>
        <p>You have requested to reset your password. Click the button below to proceed:</p>
        <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
        <p>If you did not request this, please ignore this email.</p>
        <p class="footer">Regards, <br> <strong>{{ config('app.name') }}</strong></p>
    </div>

</body>
</html>
