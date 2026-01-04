<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProductPerformanceStats extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        // Top selling product
        $topProduct = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->first();

        // Most viewed product
        $mostViewed = Product::orderByDesc('views')->first();

        // Products with discount
        $discountedProducts = Product::where('previous_price', '>', 0)->count();

        return [
            Stat::make('Top Selling Product', $topProduct ? $topProduct->product?->name : 'N/A')
                ->description($topProduct ? $topProduct->total_sold . ' units sold' : 'No sales yet')
                ->color('success')
                ->icon('heroicon-o-trophy'),

            Stat::make('Most Viewed Product', $mostViewed ? $mostViewed->name : 'N/A')
                ->description($mostViewed ? $mostViewed->views . ' views' : 'No views')
                ->color('info')
                ->icon('heroicon-o-eye'),

            Stat::make('Discounted Products', number_format($discountedProducts))
                ->description('Products with previous price')
                ->color('warning')
                ->icon('heroicon-o-tag'),
        ];
    }
}
