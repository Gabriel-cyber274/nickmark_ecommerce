<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to <?php echo e($appName); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        .welcome-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 12px;
            color: white;
            text-align: center;
            margin: 20px 0;
        }

        .welcome-box h1 {
            margin: 0 0 10px 0;
            font-size: 2em;
        }

        .content-box {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }

        .feature {
            flex: 1;
            min-width: 150px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
            text-align: center;
        }

        .feature-icon {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?php echo e($message->embed(public_path('assets/images/demos/demo-4/logo.png'))); ?>"
            alt="<?php echo e($appName); ?>" class="logo">
    </div>

    <div class="welcome-box">
        <h1>Welcome, <?php echo e($name); ?>! 🎉</h1>
        <p>Thank you for joining <strong><?php echo e($appName); ?></strong></p>
    </div>

    <p>Hi <?php echo e($name); ?>,</p>

    <p>Welcome to <strong><?php echo e($appName); ?></strong>! We're thrilled to have you on board. Your account has been successfully created with email: <strong><?php echo e($email); ?></strong>.</p>

    <div class="content-box">
        <h3>What you can do now:</h3>
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🛍️</div>
                <strong>Shop Products</strong><br>
                <small>Browse our latest collection</small>
            </div>
            <div class="feature">
                <div class="feature-icon">❤️</div>
                <strong>Wishlist</strong><br>
                <small>Save items for later</small>
            </div>
            <div class="feature">
                <div class="feature-icon">📦</div>
                <strong>Track Orders</strong><br>
                <small>View your order history</small>
            </div>
            <div class="feature">
                <div class="feature-icon">🚚</div>
                <strong>Fast Delivery</strong><br>
                <small>Get items delivered to you</small>
            </div>
        </div>

        <a href="<?php echo e($appUrl); ?>/shop" class="btn">Start Shopping</a>
    </div>

    <p>If you have any questions or need assistance, our customer support team is here to help. Just reply to this email or contact us at <strong><?php echo e(config('app.admin_email')); ?></strong>.</p>

    <div class="footer">
        <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($appName); ?>. All rights reserved.</p>
        <p>This email was sent to <?php echo e($email); ?>. If you didn't create an account, please ignore this email.</p>
    </div>
</body>

</html><?php /**PATH D:\Desktop-projects\nickmark-ecommerce\resources\views/emails/welcome.blade.php ENDPATH**/ ?>