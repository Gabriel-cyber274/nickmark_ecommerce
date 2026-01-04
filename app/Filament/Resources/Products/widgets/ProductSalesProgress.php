<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductSalesProgress extends BaseWidget
{
    public Product $record;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Completed orders (exclude pending & cancelled)
        $completedItems = $this->record->orderItems()
            ->with('order', 'product')
            ->whereHas('order', fn($query) => $query->whereNotIn('status', ['pending', 'cancelled']))
            ->get();

        $totalQuantity = $completedItems->sum('quantity');
        $totalRevenue = $completedItems->sum(fn($item) => $item->quantity * ($item->product?->price ?? 0));
        $totalOrders = $completedItems->pluck('order_id')->unique()->count();
        $averageQuantity = $totalOrders > 0 ? $totalQuantity / $totalOrders : 0;

        // Pending orders
        $pendingItems = $this->record->orderItems()
            ->with('order', 'product')
            ->whereHas('order', fn($query) => $query->where('status', 'pending'))
            ->get();

        $pendingQuantity = $pendingItems->sum('quantity');
        $pendingRevenue = $pendingItems->sum(fn($item) => $item->quantity * ($item->product?->price ?? 0));
        $pendingOrders = $pendingItems->pluck('order_id')->unique()->count();

        // Cancelled orders
        $cancelledItems = $this->record->orderItems()
            ->with('order', 'product')
            ->whereHas('order', fn($query) => $query->where('status', 'cancelled'))
            ->get();

        $cancelledQuantity = $cancelledItems->sum('quantity');
        $cancelledRevenue = $cancelledItems->sum(fn($item) => $item->quantity * ($item->product?->price ?? 0));
        $cancelledOrders = $cancelledItems->pluck('order_id')->unique()->count();

        // Optional: sales goal (for progress bar)
        $salesGoal = $this->record->sales_goal ?? 100;
        $percentage = $salesGoal > 0 ? ($totalRevenue / $salesGoal) * 100 : 0;
        $percentage = min($percentage, 100);

        $color = match (true) {
            $percentage >= 100 => 'success',
            $percentage >= 75 => 'info',
            $percentage >= 50 => 'warning',
            default => 'gray',
        };

        $chartData = [];
        for ($i = 0; $i <= 100; $i += 10) {
            $chartData[] = $i <= $percentage ? $percentage : 0;
        }

        return [
            // Completed orders stats
            Stat::make('Revenue Progress', "₦" . number_format($totalRevenue, 2))
                ->description("Across {$totalOrders} orders")
                ->descriptionIcon($percentage >= 100 ? 'heroicon-o-trophy' : 'heroicon-o-arrow-up-right')
                ->color($color)
                ->chart($chartData)
                ->chartColor($color),

            Stat::make('Total Quantity Sold', $totalQuantity)
                ->description("Across {$totalOrders} orders")
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color($totalQuantity > 0 ? 'success' : 'gray'),

            Stat::make('Average Quantity per Order', number_format($averageQuantity, 2))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($averageQuantity > 0 ? 'info' : 'gray'),

            Stat::make('Total Orders', $totalOrders)
                ->descriptionIcon('heroicon-o-document-text')
                ->color($totalOrders > 0 ? 'primary' : 'gray'),

            // Pending orders stats
            Stat::make('Pending Orders', $pendingOrders)
                ->description("Qty: {$pendingQuantity}, Revenue: ₦" . number_format($pendingRevenue, 2))
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'gray'),

            // Cancelled orders stats
            Stat::make('Cancelled Orders', $cancelledOrders)
                ->description("Qty: {$cancelledQuantity}, Revenue: ₦" . number_format($cancelledRevenue, 2))
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($cancelledOrders > 0 ? 'danger' : 'gray'),
        ];
    }
}
