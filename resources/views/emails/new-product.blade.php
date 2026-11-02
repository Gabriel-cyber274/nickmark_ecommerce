<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Product Alert - {{ $appName }}</title>
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

        .alert-badge {
            background: #28a745;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        .product-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            margin: 20px 0;
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #f5f5f5;
        }

        .product-details {
            padding: 25px;
        }

        .product-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 10px 0;
        }

        .product-price {
            font-size: 1.3em;
            color: #e74c3c;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1em;
            margin-left: 10px;
        }

        .product-description {
            color: #666;
            margin: 15px 0;
        }

        .product-meta {
            display: flex;
            gap: 20px;
            font-size: 0.9em;
            color: #888;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 14px 35px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }

        .unsubscribe {
            color: #999;
            font-size: 0.85em;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ $message->embed(public_path('assets/images/demos/demo-4/logo.png')) }}"
            alt="{{ $appName }}" class="logo">
        <span class="alert-badge">🆕 New Product Alert</span>
    </div>

    <p>Hello!</p>

    <p>We're excited to announce a brand new product has just been added to <strong>{{ $appName }}</strong>!</p>

    <div class="product-card">
        @if ($product->images->first())
            <img src="{{ $product->images->first()->image_url }}"
                alt="{{ $product->name }}" class="product-image">
        @else
            <div class="product-image" style="display:flex;align-items:center;justify-content:center;color:#999;">
                No Image Available
            </div>
        @endif

        <div class="product-details">
            <h2 class="product-name">{{ $product->name }}</h2>

            <div class="product-price">
                ₦{{ number_format($product->price, 2) }}
                @if ($product->previous_price && $product->previous_price > $product->price)
                    <span class="product-old-price">₦{{ number_format($product->previous_price, 2) }}</span>
                @endif
            </div>

            <div class="product-description">
                {{ $product->description }}
            </div>

            <div class="product-meta">
                @if ($product->category)
                    <span>📂 {{ $product->category->name }}</span>
                @endif
                @if ($product->brand_name)
                    <span>🏷️ {{ $product->brand_name }}</span>
                @endif
                @if ($product->is_new)
                    <span class="alert-badge" style="font-size:0.75em;padding:4px 10px;">NEW</span>
                @endif
                @if ($product->is_featured)
                    <span class="alert-badge" style="font-size:0.75em;padding:4px 10px;background:#ffc107;color:#333;">FEATURED</span>
                @endif
            </div>

            <div style="text-align:center;">
                <a href="{{ $appUrl }}/product/{{ $product->id }}" class="btn">View Product</a>
            </div>
        </div>
    </div>

    <p>Be among the first to get this amazing product. <strong>Limited stock available!</strong></p>

    <div class="footer">
        <p>Happy shopping!<br>The {{ $appName }} Team</p>
        <p class="unsubscribe">
            You're receiving this because you have an account with us.
            <br>To stop receiving product notifications, <a href="{{ $appUrl }}/account/notifications">manage your preferences</a>.
        </p>
        <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
    </div>
</body>

</html>