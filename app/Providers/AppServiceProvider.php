<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Bank;
use App\Models\Category;
use App\Models\CompanyReview;
use App\Models\ContactUs;
use App\Models\DiscountCode;
use App\Models\DispatchFee;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\User;
use App\Observers\AdminActionObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        collect([
            Product::class,
            Category::class,
            Order::class,
            Bank::class,
            User::class,
            Admin::class,
            Setting::class,
            DiscountCode::class,
            DispatchFee::class,
            Faq::class,
            CompanyReview::class,
            ProductReview::class,
            ContactUs::class,
        ])->each(function (string $model): void {
            $model::observe(AdminActionObserver::class);
        });
    }
}
