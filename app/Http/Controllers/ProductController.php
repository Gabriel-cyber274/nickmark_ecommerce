<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Inertia\Response
     */
    public function show(Product $product)
    {
        // Load product with relationships
        $product->load(['category', 'images', 'answers.question', 'reviews.user']);

        // Increment view count
        $product->increment('views');

        $answerMap = $product->answers->map(fn($a) => [
            'category_question_id' => $a->category_question_id,
            'answer' => $a->answer,
        ])->values();

        $recommendProducts = Product::with(['images', 'category'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->whereHas('answers', function ($q) use ($answerMap) {
                foreach ($answerMap as $answer) {
                    $q->orWhere(function ($sub) use ($answer) {
                        $sub->where('category_question_id', $answer['category_question_id'])
                            ->where('answer', $answer['answer']);
                    });
                }
            })
            ->get()
            ->sortBy(function ($p) use ($product, $answerMap) {
                $matchScore = $p->answers->filter(function ($a) use ($answerMap) {
                    return $answerMap->contains(
                        fn($x) =>
                        $x['category_question_id'] == $a->category_question_id &&
                            $x['answer'] == $a->answer
                    );
                })->count();

                $priceScore = abs($product->price - $p->price);

                return - ($matchScore * 1000 - $priceScore);
            })
            ->take(4);

        // SEO: JSON-LD Product structured data (server-side for initial HTML)
        $reviews = $product->reviews ?? collect();
        $reviewArray = $reviews->toArray();
        $avgRating = count($reviewArray) > 0
            ? (array_sum(array_column($reviewArray, 'rate')) / count($reviewArray))
            : 0.0;
        $productImages = $product->images ?? collect();
        $imageUrls = $productImages->map(fn($img) => $img->image_url)->filter()->toArray();
        $category = $product->category;

        $productJsonLd = json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $imageUrls,
            'description' => $product->description ?? '',
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand_name ?? 'Nickmark'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => url('/products/' . $product->slug),
                'priceCurrency' => 'NGN',
                'price' => $product->price,
                'priceValidUntil' => Carbon::now()->addDays(30)->toISOString(),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => 'https://schema.org/InStock'
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format($avgRating, 1),
                'reviewCount' => count($reviewArray)
            ]
        ]);

        $breadcrumbJsonLd = json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => url('/')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $category->name ?? 'Categories',
                    'item' => url('/categories/' . ($category->slug ?? ''))
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $product->name,
                    'item' => url('/products/' . $product->slug)
                ]
            ]
        ]);

        return Inertia::render('ProductDetailsPage', [
            'productId' => $product->id,
            'product' => $product,
            'recommendProducts' => $recommendProducts,
            'productJsonLd' => $productJsonLd,
            'breadcrumbJsonLd' => $breadcrumbJsonLd,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'auth' => auth()->user(),
        ]);
    }

    /**
     * Store a new product review.
     */
    public function storeReview(Request $request, Product $product)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Create the review
        $review = new ProductReview();
        $review->product_id = $product->id;
        $review->name = $validated['name'];
        $review->rate = $validated['rate'];
        $review->comment = $validated['comment'];

        // If user is authenticated, associate the review with the user
        if (auth()->check()) {
            $review->user_id = auth()->id();
        }

        $review->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Thank you for your review! It has been submitted successfully.');
    }
}
