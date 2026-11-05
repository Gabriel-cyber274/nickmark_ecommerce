<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting us</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, #4f6df5 0%, #3a56d5 100%);
            padding: 25px 30px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo {
            max-height: 50px;
            width: auto;
        }

        .email-title {
            color: white;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-content {
            padding: 30px;
        }

        .section-title {
            color: #3a56d5;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
        }

        .message-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }

        .info-item {
            flex: 1 1 calc(50% - 15px);
            min-width: 200px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-value {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 3px solid #4f6df5;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #eaeaea;
            font-size: 13px;
            color: #777;
        }

        @media (max-width: 600px) {
            .info-item {
                flex: 1 1 100%;
            }

            .email-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo-container">
                <img src="{{ asset('assets/images/demos/demo-4/logo.png') }}" alt="Company Logo" class="logo">
            </div>
            <h1 class="email-title">Message Received</h1>
        </div>

        <div class="email-content">
            <p>Hello <strong>{{ $contact->name }}</strong>,</p>

            <p>Thank you for reaching out to NickMark Technologies Ltd. We have received your message and our team will
                get back to you as soon as possible.</p>

            <h2 class="section-title">Your Message Summary</h2>

            <div class="message-info">
                <div class="info-item">
                    <div class="info-label">Subject</div>
                    <div class="info-value">{{ $contact->subject ?: 'Not specified' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $contact->email }}</div>
                </div>

                @if ($contact->phone)
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $contact->phone }}</div>
                    </div>
                @endif
            </div>

            <div class="message-info">
                <div class="info-item" style="flex: 1 1 100%;">
                    <div class="info-label">Message:</div>
                    <div class="info-value" style="white-space: pre-wrap;">{{ $contact->content }}</div>
                </div>
            </div>

            <p>If you have any additional questions, feel free to reply to this email or contact us at
                <a href="mailto:{{ config('app.admin_email') }}">{{ config('app.admin_email') }}</a>.</p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>11, Euro 65 Berger Yard, Berger Yard Bus/stop, Apapa Oshodi Expressway, Apapa, Lagos, Nigeria.</p>
        </div>
    </div>
</body>

</html>
