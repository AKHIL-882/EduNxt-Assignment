<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin: 20px 0;
            padding: 15px 30px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hello, <strong>{{ $name }}</strong>,</p>
            <p>You requested a password reset and this is valid for next 60 Minutes. Click the button below to reset your password:</p>
            <a href="http://localhost:5173/new-password/?token={{$token}}" class="btn">Reset Password</a>
            <p>If you did not request this, please ignore this email. Your password will remain unchanged.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} online dashboard. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
