<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order Received - {{ config('app.name') }}</title>
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

        .alert {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            border-left: 4px solid #c62828;
        }

        .order-details {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-weight: bold;
            font-size: 1.2em;
            color: #2c3e50;
        }

        .customer-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ $message->embed(public_path('assets/images/demos/demo-4/logo.png')) }}"
            alt="{{ config('app.name') }}" class="logo">
        <h1>New Order Received</h1>
    </div>

    <div class="alert">
        ⚡ ACTION REQUIRED: A new order has been placed and requires processing.
    </div>

    <div class="customer-info">
        <h2>Customer Information</h2>
        <p><strong>Customer:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone }}</p>

        @if ($order->user)
            <p><strong>Registered User:</strong> Yes (ID: {{ $order->user->id }})</p>
        @else
            <p><strong>Registered User:</strong> No (Guest Order)</p>
        @endif
    </div>

    <div class="order-details">
        <h2>Order Summary</h2>
        <p><strong>Order ID:</strong> #{{ $order->id }}</p>
        <p><strong>Reference:</strong> {{ $order->reference }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        <p><strong>Total Amount:</strong> <span class="total">₦{{ number_format($order->total, 2) }}</span></p>
    </div>

    <div class="order-details">
        <h3>Shipping Details</h3>
        <p><strong>Address:</strong> {{ $order->address }}</p>
        @if ($order->city)
            <p><strong>City:</strong> {{ $order->city->name }}</p>
        @endif
        @if ($order->state)
            <p><strong>State:</strong> {{ $order->state->name }}</p>
        @endif
        <p><strong>Postal Code:</strong> {{ $order->postal_code }}</p>
        <p><strong>Delivery Method:</strong>
            {{ $order->delivery_method === 'pickup' ? 'Customer Pickup' : 'Home Delivery' }}</p>
    </div>

    <div class="order-details">
        <h3>Order Items ({{ $order->items->count() }})</h3>
        @foreach ($order->items as $item)
            <div class="order-item">
                <div>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small>Product ID: {{ $item->product_id }}</small><br>
                    <small>Quantity: {{ $item->quantity }}</small>
                </div>
                <div>
                    ₦{{ number_format($item->product->price, 2) }} × {{ $item->quantity }}<br>
                    <strong>₦{{ number_format($item->product->price * $item->quantity, 2) }}</strong>
                </div>
            </div>
        @endforeach

        <div class="order-item">
            <div><strong>Subtotal:</strong></div>
            <div>₦{{ number_format($order->subtotal, 2) }}</div>
        </div>

        @if ($order->discount)
            <div class="order-item">
                <div><strong>Discount Applied:</strong></div>
                <div>₦{{ number_format($order->total - $order->subtotal, 2) }}<br>
                    <small>Code: {{ $order->discount->code }}</small>
                </div>
            </div>
        @endif

        <div class="order-item total">
            <div><strong>Total Amount:</strong></div>
            <div>₦{{ number_format($order->total, 2) }}</div>
        </div>
    </div>

    @if ($order->order_note)
        <div class="order-details">
            <h3>Customer Note:</h3>
            <p>{{ $order->order_note }}</p>
        </div>
    @endif

    <center>
        {{-- <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">
            View Order in Admin Panel
        </a> --}}
        <a href="" class="btn">
            View Order in Admin Panel
        </a>
    </center>

    <p><strong>Next Steps:</strong></p>
    <ul>
        <li>Review the order details</li>
        <li>Prepare items for {{ $order->delivery_method === 'pickup' ? 'pickup' : 'shipping' }}</li>
        <li>Update order status when processed</li>
        <li>Contact customer if any issues</li>
    </ul>

    <p>This is an automated notification. Please do not reply to this email.</p>
</body>

</html>
