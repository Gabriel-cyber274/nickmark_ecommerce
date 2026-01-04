<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation - {{ config('app.name') }}</title>
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

        .order-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .total {
            font-weight: bold;
            font-size: 1.2em;
            color: #2c3e50;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            background: #28a745;
            color: white;
            font-weight: bold;
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
        <img src="{{ $message->embed(public_path('assets/images/demos/demo-4/logo.png')) }}"
            alt="{{ config('app.name') }}" class="logo">
        <h1>Order Confirmation</h1>
    </div>

    <p>Hello {{ $order->name }},</p>

    <p>Thank you for your order! We're pleased to confirm that your payment was successful and your order has been
        received.</p>

    <div class="order-details">
        <h2>Order Details</h2>
        <p><strong>Order Reference:</strong> {{ $order->reference }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        <p><strong>Status:</strong> <span class="status-badge">{{ ucfirst($order->status) }}</span></p>

        <h3>Shipping Information</h3>
        <p><strong>Name:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone }}</p>
        <p><strong>Address:</strong> {{ $order->address }}</p>
        @if ($order->city)
            <p><strong>City:</strong> {{ $order->city->name }}</p>
        @endif
        @if ($order->state)
            <p><strong>State:</strong> {{ $order->state->name }}</p>
        @endif
        <p><strong>Postal Code:</strong> {{ $order->postal_code }}</p>
    </div>

    <div class="order-details">
        <h3>Order Items</h3>
        @foreach ($order->items as $item)
            <div class="order-item">
                <div>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small>Quantity: {{ $item->quantity }}</small>
                </div>
                <div>
                    ₦{{ number_format($item->product->price * $item->quantity, 2) }}
                </div>
            </div>
        @endforeach

        <div class="order-item">
            <div><strong>Subtotal:</strong></div>
            <div>₦{{ number_format($order->subtotal, 2) }}</div>
        </div>

        @if ($order->discount)
            <div class="order-item">
                <div><strong>Discount ({{ $order->discount->code }}):</strong></div>
                <div>₦{{ number_format($order->total - $order->subtotal, 2) }}</div>
            </div>
        @endif

        <div class="order-item total">
            <div><strong>Total:</strong></div>
            <div>₦{{ number_format($order->total, 2) }}</div>
        </div>
    </div>

    <p><strong>Delivery Method:</strong> {{ $order->delivery_method === 'pickup' ? 'Pickup' : 'Home Delivery' }}</p>

    @if ($order->order_note)
        <div class="order-details">
            <h3>Your Note:</h3>
            <p>{{ $order->order_note }}</p>
        </div>
    @endif

    <div class="footer">
        <p>If you have any questions, please contact our customer support at {{ config('app.admin_email') }}</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>
