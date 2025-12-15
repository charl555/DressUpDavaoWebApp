<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset - DressUp Davao</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-height: 60px;
            margin-bottom: 20px;
        }

        .code-box {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            border: 2px dashed #7c3aed;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
            font-family: 'Courier New', monospace;
        }

        .code {
            font-size: 42px;
            font-weight: bold;
            color: #7c3aed;
            letter-spacing: 8px;
            margin: 10px 0;
        }

        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            background: #7c3aed;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #7c3aed; margin-bottom: 10px;">Password Reset Request</h2>
            <p style="color: #6b7280; margin-bottom: 0;">DressUp Davao Account Security</p>
        </div>

        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        <p>We received a request to reset the password for your DressUp Davao account.</p>

        <div class="code-box">
            <p style="margin-bottom: 10px; color: #4b5563;">Your 6-digit verification code:</p>
            <div class="code">{{ $code }}</div>
            <p style="margin-top: 10px; color: #6b7280; font-size: 14px;">Valid for 10 minutes</p>
        </div>

        <div class="warning">
            <strong>Important Security Notice:</strong>
            <p style="margin: 5px 0;">This code was requested from IP: <code>{{ $ip }}</code></p>
            <p style="margin: 5px 0;">If you didn't request this password reset, please secure your account immediately.
            </p>
        </div>

        <p style="margin-top: 30px;">
            Enter this code on the password reset page to create a new password.
        </p>

        <div class="footer">
            <p style="margin-bottom: 5px;">
                <strong>Need help?</strong> Reply to this email or contact support.
            </p>
            <p style="margin: 0; font-size: 13px;">
                This is an automated message. Please do not reply directly.
            </p>
            <p style="margin-top: 20px; color: #9ca3af; font-size: 12px;">
                © {{ date('Y') }} DressUp Davao. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>