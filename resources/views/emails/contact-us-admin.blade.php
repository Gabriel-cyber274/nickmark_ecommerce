<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        /* Base styles */
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

        /* Header */
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

        /* Content */
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

        /* Message content */
        .message-container {
            margin-top: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #eaeaea;
        }

        .message-text {
            white-space: pre-wrap;
            line-height: 1.7;
            margin-top: 10px;
            color: #444;
        }

        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #eaeaea;
            font-size: 13px;
            color: #777;
        }

        .timestamp {
            margin-top: 10px;
            font-style: italic;
        }

        /* Responsive adjustments */
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
        <!-- Header with logo -->
        <div class="email-header">
            <div class="logo-container">
                <img src="{{ asset('assets/images/demos/demo-4/logo.png') }}" alt="Company Logo" class="logo">
            </div>
            <h1 class="email-title">New Contact Message</h1>
        </div>

        <!-- Main content -->
        <div class="email-content">
            <h2 class="section-title">Contact Information</h2>

            <div class="message-info">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $contact->name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $contact->email }}</div>
                </div>

                @if ($contact->phone)
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">{{ $contact->phone }}</div>
                    </div>
                @endif

                @if ($contact->subject)
                    <div class="info-item">
                        <div class="info-label">Subject</div>
                        <div class="info-value">{{ $contact->subject }}</div>
                    </div>
                @endif
            </div>

            <h2 class="section-title">Message Content</h2>

            <div class="message-container">
                <div class="info-label">Message:</div>
                <div class="message-text">{{ $contact->content }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>This message was sent via the Contact Us form on your website.</p>
            <p class="timestamp">Received: {{ date('F j, Y, g:i a') }}</p>
        </div>
    </div>
</body>

</html>
