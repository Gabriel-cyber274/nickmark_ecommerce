<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function initiatePaystack(Request $request, PaystackService $paystack)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'delivery_method' => 'required|in:pickup,dispatch',
            'shipping_fee' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string',
            'notes' => 'nullable|string',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $response = $paystack->initializeTransaction(
            $validated['email'],
            $validated['total'] * 100, // convert to kobo
            route('paystack.callback'),
            [
                'order_data' => $validated,
                'user_id' => auth()->id(), // This returns null if not authenticated
            ]
        );

        return response()->json([
            'authorization_url' => $response['data']['authorization_url'],
            'reference' => $response['data']['reference']
        ]);
    }

    public function handlePaystackCallback(Request $request, PaystackService $paystack)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('checkout')
                ->with('error', 'Payment reference missing');
        }

        $response = $paystack->verifyTransaction($reference);

        if ($response['data']['status'] !== 'success') {
            return redirect()->route('checkout')
                ->with('error', 'Payment verification failed');
        }

        $data = $response['data'];
        $metadata = $data['metadata'] ?? [];
        $orderData = $metadata['order_data'] ?? [];

        if (empty($orderData)) {
            return redirect()->route('checkout')
                ->with('error', 'Order data not found');
        }

        try {
            DB::beginTransaction();

            // Get user_id from metadata, ensure it's null if not set
            $userId = isset($metadata['user_id']) && !empty($metadata['user_id'])
                ? $metadata['user_id']
                : null;

            // Create the order
            $order = $this->createOrder($orderData, 'paystack', $reference, $userId);

            DB::commit();

            // Send emails
            $this->sendOrderEmails($order);


            // Clear cart if user is authenticated
            if (auth()->check()) {
                auth()->user()->cartItems()->delete();
            }

            return redirect()->route('home')
                ->with('success', 'Payment successful! Your order has been placed.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout')
                ->with('error', 'Failed to process order. Please contact support.');
        }
    }


    protected function sendOrderEmails(Order $order): void
    {
        try {
            // Send confirmation email to customer
            Mail::to($order->email)
                ->send(new OrderConfirmationMail($order));

            // Send notification email to admin
            $adminEmail = config('app.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new AdminNewOrderMail($order));
            }

            Log::info('Order emails sent successfully', [
                'order_id' => $order->id,
                'customer_email' => $order->email,
                'admin_email' => $adminEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order emails', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }


    public function createWhatsAppOrder(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'delivery_method' => 'required|in:pickup,dispatch',
            'shipping_fee' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string',
            'notes' => 'nullable|string',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|integer',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $reference = 'WA-' . strtoupper(Str::random(10));

            // Ensure user_id is null if not authenticated
            $userId = auth()->check() ? auth()->id() : null;

            $order = $this->createOrder($validated, 'whatsapp', $reference, $userId);

            DB::commit();

            // Clear cart if user is authenticated
            if (auth()->check()) {
                auth()->user()->cartItems()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'reference' => $order->reference
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('WhatsApp order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function createOrder(array $data, string $paymentMethod, string $reference, $userId = null)
    {
        // Find state and city IDs
        $state = \App\Models\State::where('name', $data['state'])->first();
        $city = $state ? \App\Models\City::where('name', $data['city'])
            ->where('state_id', $state->id)
            ->first() : null;

        // Find discount code if provided
        $discountId = null;
        if (!empty($data['discount_code'])) {
            $discount = \App\Models\DiscountCode::where('code', $data['discount_code'])
                ->where('expires_at', '>', now())
                ->first();
            $discountId = $discount?->id;
        }

        // Combine address
        $fullAddress = $data['address'];
        if (!empty($data['address_line_2'])) {
            $fullAddress .= ', ' . $data['address_line_2'];
        }

        // Determine order status based on payment method
        $status = $paymentMethod === 'paystack' ? 'paid' : 'pending';

        // CRITICAL FIX: Ensure user_id is explicitly null if not provided
        // Cast empty string or false to null
        $userId = $userId ?: null;

        // Create order
        $order = Order::create([
            'user_id' => $userId, // Will be null for guest users
            'discount_id' => $discountId,
            'reference' => $reference,
            'payment_method' => $paymentMethod,
            'subtotal' => $data['subtotal'],
            'delivery_method' => $data['delivery_method'],
            'total' => $data['total'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'state_id' => $state?->id,
            'city_id' => $city?->id,
            'address' => $fullAddress,
            'postal_code' => $data['postal_code'],
            'order_note' => $data['notes'] ?? null,
            'status' => $status,
        ]);

        // Create order items
        foreach ($data['cart_items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Update or create user info if user is authenticated
        if ($userId) {
            User::find($userId)->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
            ]);

            UserInfo::updateOrCreate(
                ['user_id' => $userId],
                [
                    'phone' => $data['phone'],
                    'state_id' => $state?->id,
                    'city_id' => $city?->id,
                    'address' => $fullAddress,
                    'postal_code' => $data['postal_code'],
                ]
            );
        }

        return $order;
    }
}
