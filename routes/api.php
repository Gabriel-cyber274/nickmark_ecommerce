<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserWishlist;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/categories', function () {
    return Category::has('products')->get();
});

// Register API - Use web middleware for session support
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $order = Order::where('email', $validated['email'])
        ->update(['user_id' => $user->id]);



    // Log the user in using web guard (session-based)
    Auth::login($user);

    // Regenerate session to get new CSRF token
    $request->session()->regenerate();

    return response()->json([
        'message' => 'Registration successful',
        'user' => $user,
        'redirect' => '/dashboard',
        'csrf_token' => csrf_token(),
    ], 201);
})->middleware('web');

// Login API - Use web middleware for session support
Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Attempt to authenticate the user
    if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user(),
            'redirect' => '/dashboard',
            'csrf_token' => csrf_token(),
        ], 200);
    }

    throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
    ]);
})->middleware('web');

// Logout API - Use web middleware for session support
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logged out successfully'
    ], 200);
})->middleware(['web', 'auth']);

// Add web middleware to all wishlist routes that need authentication
Route::middleware('web')->group(function () {
    Route::get('/wishlist', function (Request $request) {
        if (auth()->check()) {
            $wishlist = UserWishlist::where('user_id', auth()->id())
                ->with(['product.images', 'product.category'])
                ->get();
            return response()->json($wishlist);
        }
        return response()->json([]);
    })->name('api.wishlist.index');

    Route::post('/wishlist/check', function (Request $request) {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        // For authenticated users, check in database
        if (auth()->check()) {
            $wishlistItems = UserWishlist::where('user_id', auth()->id())
                ->whereIn('product_id', $validated['product_ids'])
                ->pluck('product_id')
                ->toArray();

            return response()->json(['wishlist_items' => $wishlistItems]);
        }

        // For guests, return empty array
        return response()->json(['wishlist_items' => []]);
    })->name('api.wishlist.check');

    Route::post('/wishlist/toggle', function (Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (auth()->check()) {
            $wishlistItem = UserWishlist::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                return response()->json(['message' => 'Removed from wishlist', 'inWishlist' => false]);
            } else {
                UserWishlist::create([
                    'user_id' => auth()->id(),
                    'product_id' => $validated['product_id']
                ]);
                return response()->json(['message' => 'Added to wishlist', 'inWishlist' => true]);
            }
        }

        return response()->json(['message' => 'Please login to save wishlist'], 401);
    })->name('api.wishlist.toggle');

    Route::post('/wishlist/sync', function (Request $request) {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        if (auth()->check()) {
            foreach ($validated['product_ids'] as $productId) {
                UserWishlist::firstOrCreate([
                    'user_id' => auth()->id(),
                    'product_id' => $productId
                ]);
            }
            return response()->json(['message' => 'Wishlist synced successfully']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    })->name('api.wishlist.sync');
});

// This route doesn't need authentication - it's for guest users
Route::post('/wishlist/products', function (Request $request) {
    $validated = $request->validate([
        'product_ids' => 'required|array',
        'product_ids.*' => 'exists:products,id'
    ]);

    $products = Product::with(['images', 'category'])
        ->whereIn('id', $validated['product_ids'])
        ->get();

    return response()->json($products);
})->name('api.wishlist.products');




// Add these routes to your api.php file after the wishlist routes

Route::middleware('web')->group(function () {
    // Get user's cart
    Route::get('/cart', function (Request $request) {
        if (auth()->check()) {
            $cart = UserCart::where('user_id', auth()->id())
                ->with(['product.images', 'product.category'])
                ->get();
            return response()->json($cart);
        }
        return response()->json([]);
    })->name('api.cart.index');

    // Add item to cart
    Route::post('/cart/add', function (Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if (auth()->check()) {
            $cartItem = UserCart::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $validated['quantity'];
                $cartItem->save();
                return response()->json([
                    'message' => 'Cart updated',
                    'cart_item' => $cartItem
                ]);
            } else {
                $cartItem = UserCart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity']
                ]);
                return response()->json([
                    'message' => 'Added to cart',
                    'cart_item' => $cartItem
                ]);
            }
        }

        return response()->json(['message' => 'Please login to save cart'], 401);
    })->name('api.cart.add');

    // Update cart item quantity
    Route::post('/cart/update', function (Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if (auth()->check()) {
            $cartItem = UserCart::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $validated['quantity'];
                $cartItem->save();
                return response()->json([
                    'message' => 'Cart updated',
                    'cart_item' => $cartItem
                ]);
            }

            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    })->name('api.cart.update');

    // Remove item from cart
    Route::post('/cart/remove', function (Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (auth()->check()) {
            $cartItem = UserCart::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($cartItem) {
                $cartItem->delete();
                return response()->json(['message' => 'Removed from cart']);
            }

            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    })->name('api.cart.remove');

    // Sync guest cart to authenticated user
    Route::post('/cart/sync', function (Request $request) {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        if (auth()->check()) {
            foreach ($validated['items'] as $item) {
                $cartItem = UserCart::where('user_id', auth()->id())
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($cartItem) {
                    // If item exists, add to existing quantity
                    $cartItem->quantity += $item['quantity'];
                    $cartItem->save();
                } else {
                    // Create new cart item
                    UserCart::create([
                        'user_id' => auth()->id(),
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ]);
                }
            }
            return response()->json(['message' => 'Cart synced successfully']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    })->name('api.cart.sync');
});

// Get cart products for guest users
Route::post('/cart/products', function (Request $request) {
    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    $productIds = array_column($validated['items'], 'product_id');
    $products = Product::with(['images', 'category'])
        ->whereIn('id', $productIds)
        ->get();

    // Add quantity to each product
    $productsWithQuantity = $products->map(function ($product) use ($validated) {
        $item = collect($validated['items'])->firstWhere('product_id', $product->id);
        $product->quantity = $item['quantity'];
        return $product;
    });

    return response()->json($productsWithQuantity);
})->name('api.cart.products');
