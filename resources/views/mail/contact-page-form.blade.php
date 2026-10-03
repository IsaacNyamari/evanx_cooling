{{-- resources/views/emails/contact-form.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }

        .detail-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-label {
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
        }

        .message-box {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
            margin-top: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .badge {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📬 New Contact Form Message</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">{{ config('app.name') }}</p>
        </div>

        <div class="content">
            <div class="badge">New Inquiry</div>

            <div class="detail-row">
                <div class="detail-label">📋 Subject</div>
                <div class="detail-value"><strong>{{ $msubject }}</strong></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">👤 Name</div>
                <div class="detail-value">{{ $name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">📧 Email Address</div>
                <div class="detail-value">
                    <a href="mailto:{{ $email }}"
                        style="color: #0d6efd; text-decoration: none;">{{ $email }}</a>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">📧 Phone Address</div>
                <div class="detail-value">
                    <a href="mailto:{{ $phone }}"
                        style="color: #0d6efd; text-decoration: none;">{{ $phone }}</a>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">💬 Message</div>
                <div class="message-box">
                    {{ $umessage }}
                </div>
            </div>

            <div class="footer">
                <p>This message was sent from the contact form on {{ config('app.name') }} website.</p>
                <p>Reply directly to <a href="mailto:{{ $email }}"
                        style="color: #0d6efd;">{{ $email }}</a> to respond to this inquiry.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>
