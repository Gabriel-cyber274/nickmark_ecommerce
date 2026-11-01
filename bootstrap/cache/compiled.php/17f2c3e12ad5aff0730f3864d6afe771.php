<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order Received - <?php echo e(config('app.name')); ?></title>
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
        <img src="<?php echo e($message->embed(public_path('assets/images/demos/demo-4/logo.png'))); ?>"
            alt="<?php echo e(config('app.name')); ?>" class="logo">
        <h1>New Order Received</h1>
    </div>

    <div class="alert">
        ⚡ ACTION REQUIRED: A new order has been placed and requires processing.
    </div>

    <div class="customer-info">
        <h2>Customer Information</h2>
        <p><strong>Customer:</strong> <?php echo e($order->name); ?></p>
        <p><strong>Email:</strong> <?php echo e($order->email); ?></p>
        <p><strong>Phone:</strong> <?php echo e($order->phone); ?></p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->user): ?>
            <p><strong>Registered User:</strong> Yes (ID: <?php echo e($order->user->id); ?>)</p>
        <?php else: ?>
            <p><strong>Registered User:</strong> No (Guest Order)</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="order-details">
        <h2>Order Summary</h2>
        <p><strong>Order ID:</strong> #<?php echo e($order->id); ?></p>
        <p><strong>Reference:</strong> <?php echo e($order->reference); ?></p>
        <p><strong>Date:</strong> <?php echo e($order->created_at->format('F j, Y \a\t g:i A')); ?></p>
        <p><strong>Payment Method:</strong> <?php echo e(ucfirst($order->payment_method)); ?></p>
        <p><strong>Status:</strong> <?php echo e(ucfirst($order->status)); ?></p>
        <p><strong>Total Amount:</strong> <span class="total">₦<?php echo e(number_format($order->total, 2)); ?></span></p>
    </div>

    <div class="order-details">
        <h3>Shipping Details</h3>
        <p><strong>Address:</strong> <?php echo e($order->address); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->city): ?>
            <p><strong>City:</strong> <?php echo e($order->city->name); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->state): ?>
            <p><strong>State:</strong> <?php echo e($order->state->name); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <p><strong>Postal Code:</strong> <?php echo e($order->postal_code); ?></p>
        <p><strong>Delivery Method:</strong>
            <?php echo e($order->delivery_method === 'pickup' ? 'Customer Pickup' : 'Home Delivery'); ?></p>
    </div>

    <div class="order-details">
        <h3>Order Items (<?php echo e($order->items->count()); ?>)</h3>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="order-item">
                <div>
                    <strong><?php echo e($item->product->name); ?></strong><br>
                    <small>Product ID: <?php echo e($item->product_id); ?></small><br>
                    <small>Quantity: <?php echo e($item->quantity); ?></small>
                </div>
                <div>
                    ₦<?php echo e(number_format($item->product->price, 2)); ?> × <?php echo e($item->quantity); ?><br>
                    <strong>₦<?php echo e(number_format($item->product->price * $item->quantity, 2)); ?></strong>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="order-item">
            <div><strong>Subtotal:</strong></div>
            <div>₦<?php echo e(number_format($order->subtotal, 2)); ?></div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount): ?>
            <div class="order-item">
                <div><strong>Discount Applied:</strong></div>
                <div>₦<?php echo e(number_format($order->total - $order->subtotal, 2)); ?><br>
                    <small>Code: <?php echo e($order->discount->code); ?></small>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="order-item total">
            <div><strong>Total Amount:</strong></div>
            <div>₦<?php echo e(number_format($order->total, 2)); ?></div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->order_note): ?>
        <div class="order-details">
            <h3>Customer Note:</h3>
            <p><?php echo e($order->order_note); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->receipt): ?>
        <div class="order-details">
            <h3>Payment Receipt:</h3>
            <p>
                <img src="<?php echo e(asset('storage/' . $order->receipt)); ?>" alt="Payment Receipt" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px;">
            </p>
            <p><a href="<?php echo e(asset('storage/' . $order->receipt)); ?>" target="_blank">View Full Size</a></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <center>
        
        <a href="<?php echo e(url('admin/orders/' . $order->id)); ?>" class="btn">
            View Order in Admin Panel
        </a>
    </center>

    <p><strong>Next Steps:</strong></p>
    <ul>
        <li>Review the order details</li>
        <li>Prepare items for <?php echo e($order->delivery_method === 'pickup' ? 'pickup' : 'shipping'); ?></li>
        <li>Update order status when processed</li>
        <li>Contact customer if any issues</li>
    </ul>

    <p>This is an automated notification. Please do not reply to this email.</p>
</body>

</html>
<?php /**PATH D:\Desktop-projects\nickmark-ecommerce\resources\views/emails/new-order.blade.php ENDPATH**/ ?>