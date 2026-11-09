<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Mail\ContactUsAdminMail;
use App\Mail\ContactUsUserMail;
use App\Models\Category;
use App\Models\CompanyReview;
use App\Models\ContactUs;
use App\Models\DiscountCode;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\State;
use App\Models\UserWishlist;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; // Make sure this is at the top of your routes file
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    $categories = Category::withSum('products', 'views')->get();

    // Get unique total view counts
    $uniqueViewCounts = $categories
        ->pluck('products_sum_views')
        ->unique();

    // If views differ, sort & pick top 6
    if ($uniqueViewCounts->count() > 1) {
        $categories = $categories
            ->sortByDesc('products_sum_views')
            ->take(6)
            ->values();
    }

    $featuredProducts = Product::with(['answers', 'images', 'reviews'])
        ->where('is_featured', true)
        ->orderBy('created_at', 'desc')
        ->get();

    $newProducts = Product::with(['category', 'images', 'answers', 'reviews'])
        ->where('is_new', true)
        ->orderByDesc('created_at')
        ->take(10)
        ->get();

    $categoriesWithNewProducts = Category::whereHas('products', function ($query) {
        $query->where('is_new', true);
    })
        ->withCount([
            'products as new_products_count' => function ($query) {
                $query->where('is_new', true);
            }
        ])
        ->with([
            'products' => function ($query) {
                $query->where('is_new', true)
                    ->with(['category', 'images', 'answers'])
                    ->orderByDesc('created_at')
                    ->take(10);
            }
        ])
        ->orderByDesc('new_products_count')
        ->take(5)
        ->get();


    $newProductCategories = collect([
        'all' => $newProducts,
    ]);

    $categoriesWithNewProducts->each(function ($category) use (&$newProductCategories) {
        $newProductCategories->put(
            $category->name,
            $category->products
        );
    });


    $recommendedProducts = Product::with(['category', 'images', 'answers', 'reviews'])
        ->orderByDesc('views')
        ->take(8)
        ->get();


    return Inertia::render('Welcome', [
        'popularCategories' => $categories,
        'featuredProducts' => $featuredProducts,
        'newProductCategories' => $newProductCategories,
        'recommendedProducts' => $recommendedProducts,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
})->name('home');




Route::get('/categories', function (Request $request) {
    $categories = Category::has('products')->withCount('products')->get();

    // Get overall price range from all products
    $priceRange = [
        'min' => (int) Product::min('price'),
        'max' => (int) Product::max('price')
    ];

    // Start building the query
    $query = Product::with(['category', 'images', 'answers.question', 'reviews']);

    // Search functionality - searches across products, categories, and answers
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;

        $query->where(function ($q) use ($searchTerm) {
            // Search in product name and description
            $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                ->orWhere('brand_name', 'LIKE', "%{$searchTerm}%")

                // Search in category name
                ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                    $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                })

                // Search in product answers
                ->orWhereHas('answers', function ($answerQuery) use ($searchTerm) {
                    $answerQuery->where('answer', 'LIKE', "%{$searchTerm}%");
                });
        });
    }

    // Filter by categories
    $selectedCategories = [];
    if ($request->has('categories') && !empty($request->categories)) {
        $categoryIds = is_array($request->categories) ? $request->categories : [$request->categories];
        $query->whereIn('category_id', $categoryIds);
        $selectedCategories = $categoryIds;
    }

    // Filter by brands
    if ($request->has('brands') && !empty($request->brands)) {
        $brands = is_array($request->brands) ? $request->brands : [$request->brands];
        $query->whereIn('brand_name', $brands);
    }

    // Filter by price range
    if ($request->has('min_price') && $request->min_price != $priceRange['min']) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->has('max_price') && $request->max_price != $priceRange['max']) {
        $query->where('price', '<=', $request->max_price);
    }

    // Apply sorting
    $sort = $request->get('sort', 'date');
    switch ($sort) {
        case 'popularity':
            $query->orderBy('views', 'desc');
            break;
        case 'price_low':
            $query->orderBy('price', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price', 'desc');
            break;
        case 'name':
            $query->orderBy('name', 'asc');
            break;
        case 'date':
        default:
            $query->orderBy('id', 'desc');
            break;
    }

    $products = $query->paginate(9)->withQueryString();

    // Get available brands based on selected categories (or all if none selected)
    $brandsQuery = Product::select('brand_name')
        ->whereNotNull('brand_name')
        ->where('brand_name', '!=', '');

    if (!empty($selectedCategories)) {
        $brandsQuery->whereIn('category_id', $selectedCategories);
    }

    $availableBrands = $brandsQuery->distinct()
        ->pluck('brand_name')
        ->sort()
        ->values()
        ->toArray();

    return Inertia::render('Categories', [
        'categories' => $categories,
        'products' => $products,
        'availableBrands' => $availableBrands,
        'priceRange' => $priceRange,
        'filters' => [
            'categories' => $request->get('categories', []),
            'brands' => $request->get('brands', []),
            'sort' => $sort,
            'min_price' => $request->get('min_price', $priceRange['min']),
            'max_price' => $request->get('max_price', $priceRange['max']),
            'search' => $request->get('search', ''),
        ],
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
});


Route::get('/about', function () {
    $companyReview = CompanyReview::orderBy('id', 'desc')->get();

    return Inertia::render('AboutPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
        'companyReview' => $companyReview
    ]);
});

Route::get('/faq', function () {
    $faq = Faq::all();
    return Inertia::render('FaqPage', [
        'faq' => $faq,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
});

Route::get('/contact-us', function () {
    return Inertia::render('ContactUsPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
        'googleMapsApiKey' => config('app.google_maps_api_key'),
    ]);
});

Route::get('/wishlist', function () {
    $wishlist = [];
    if (auth()->check()) {
        $wishlist = UserWishlist::where('user_id', auth()->id())
            ->with(['product.images', 'product.category', 'product.answers'])
            ->get();
    }

    return Inertia::render('Wishlist', [
        'wishlist' => $wishlist,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
});


Route::get('/cart', function () {
    return Inertia::render('CartPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
});


Route::get('/checkout', function () {
    $state = State::with(['cities.dispatchFee', 'dispatchFee'])->get();

    // Only get discount codes that haven't expired yet
    $discountCodes = DiscountCode::where('expires_at', '>', Carbon::now())->get();

    return Inertia::render('CheckoutPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'stateandcity' => $state,
        'codes' => $discountCodes,
        'auth' => auth()->user()?->load('userInfo'),
    ]);
})->name('checkout');

Route::get('/settings', [\App\Http\Controllers\Api\SettingsController::class, 'index'])->name('settings.index');

// Paystack routes
Route::post('/paystack/initiate', [OrderController::class, 'initiatePaystack'])->name('paystack.initiate');
Route::get('/paystack/callback', [OrderController::class, 'handlePaystackCallback'])->name('paystack.callback');

// WhatsApp order route
Route::post('/order/whatsapp', [OrderController::class, 'createWhatsAppOrder'])->name('order.whatsapp');



Route::post('/contact-us', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'subject' => 'nullable|string|max:255',
        'message' => 'required|string',
    ]);

    $contact = ContactUs::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'subject' => $validated['subject'],
        'content' => $validated['message'],
    ]);

    $adminEmail = config('app.admin_email');

    // Queue email to admin
    Mail::to($adminEmail)->queue(new ContactUsAdminMail($contact));

    // Queue confirmation email to the user who submitted the form
    Mail::to($contact->email)->queue(new ContactUsUserMail($contact));

    return redirect()->back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
})->name('contact-us.store');



Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Redirect old product ID URLs to new slug-based URLs (SEO preservation)
Route::get('/product/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('products.show', ['product' => $product->slug], 301);
})->name('product.show.redirect');

// Category detail page with slug
Route::get('/categories/{category:slug}', function (\App\Models\Category $category) {
    $category->load(['products' => function ($query) {
        $query->with(['category', 'images', 'answers'])
            ->orderByDesc('created_at');
    }]);

    return Inertia::render('CategoryPage', [
        'category' => $category,
        'products' => $category->products,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
})->name('categories.show');

// Redirect old category ID URLs to new slug-based URLs (SEO preservation)
Route::get('/category/{id}', function ($id) {
    $category = \App\Models\Category::findOrFail($id);
    return redirect()->route('categories.show', ['category' => $category->slug], 301);
})->name('category.show.redirect');


// Review Submission Endpoint
Route::post('/products/{product:slug}/review', [ProductController::class, 'storeReview'])->name('product.review');


Route::get('/dashboard', function () {
    $user = auth()->user()->load(['userInfo.state', 'userInfo.city']);

    // Get user orders with items and product details
    $orders = auth()->user()->orders()
        ->with(['items.product.images', 'state', 'city'])
        ->latest()
        ->get();

    // Get states and cities for the address form
    $states = \App\Models\State::all();
    $cities = \App\Models\City::all();

    return Inertia::render('UserDashboardPage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => $user,
        'orders' => $orders,
        'states' => $states,
        'cities' => $cities,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/orders/{id}', function ($id) {
    $order = \App\Models\Order::with([
        'items.product.images',
        'items.product.category',
        'user',
        'state',
        'city',
        'discount'
    ])->findOrFail($id);

    // Ensure the order belongs to the authenticated user
    if (auth()->check() && $order->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access to this order.');
    }

    return Inertia::render('OrderDetails', [
        'order' => $order,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => auth()->user(),
    ]);
})->middleware(['auth', 'verified'])->name('order.details');

Route::middleware(['auth', 'verified'])->group(function () {
    // Update user account details
    Route::post('/user/update-account', [UserController::class, 'updateAccount'])
        ->name('user.update-account');

    // Update user billing/shipping address
    Route::post('/user/update-address', [UserController::class, 'updateAddress'])
        ->name('user.update-address');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Serve storage files (for Windows where symlink may not work)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($fullPath);
    $headers = [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ];
    
    return response()->file($fullPath, $headers);
})->where('path', '.*');

// Sitemap.xml for SEO
Route::get('/sitemap.xml', function () {
    $products = \App\Models\Product::whereNotNull('slug')->select('id', 'slug', 'updated_at')->get();
    $categories = \App\Models\Category::whereNotNull('slug')->select('id', 'slug', 'updated_at')->get();

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Homepage
    $sitemap .= '<url>';
    $sitemap .= '<loc>' . url('/') . '</loc>';
    $sitemap .= '<lastmod>' . now()->toISOString() . '</lastmod>';
    $sitemap .= '<changefreq>daily</changefreq>';
    $sitemap .= '<priority>1.0</priority>';
    $sitemap .= '</url>';

    // Categories
    foreach ($categories as $category) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . url('/categories/' . $category->slug) . '</loc>';
        $sitemap .= '<lastmod>' . $category->updated_at->toISOString() . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';
    }

    // Products
    foreach ($products as $product) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . url('/products/' . $product->slug) . '</loc>';
        $sitemap .= '<lastmod>' . $product->updated_at->toISOString() . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';
    }

    // Static pages
    $staticPages = [
        '/' => ['daily', 1.0],
        '/categories' => ['weekly', 0.8],
        '/about' => ['monthly', 0.5],
        '/faq' => ['monthly', 0.5],
        '/contact-us' => ['monthly', 0.5],
    ];

    foreach ($staticPages as $url => $data) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . url($url) . '</loc>';
        $sitemap .= '<lastmod>' . now()->toISOString() . '</lastmod>';
        $sitemap .= '<changefreq>' . $data[0] . '</changefreq>';
        $sitemap .= '<priority>' . $data[1] . '</priority>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';

    return response($sitemap, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// Robots.txt for SEO
Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /cart\n";
    $content .= "Disallow: /checkout\n";
    $content .= "Disallow: /dashboard\n";
    $content .= "Disallow: /orders\n";
    $content .= "Disallow: /profile\n";
    $content .= "Disallow: /wishlist\n";
    $content .= "Disallow: /paystack\n";
    $content .= "Disallow: /order\n";
    $content .= "Disallow: /user/\n";
    $content .= "Disallow: /_next/\n";
    $content .= "Disallow: /*?\n";
    $content .= "Disallow: /*&*\n";
    $content .= "\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

    return response($content, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

require __DIR__ . '/auth.php';
