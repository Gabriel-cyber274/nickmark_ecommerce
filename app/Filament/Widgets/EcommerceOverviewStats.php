<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EcommerceOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Get all counts in one query for efficiency
        $orderStats = DB::table('orders')
            ->select(
                DB::raw("SUM(CASE WHEN status IN ('paid', 'completed') THEN total ELSE 0 END) as total_revenue"),
                DB::raw("SUM(CASE WHEN status IN ('paid', 'completed') AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) THEN total ELSE 0 END) as monthly_revenue"),
                DB::raw("AVG(CASE WHEN status IN ('paid', 'completed') THEN total END) as avg_order_value"),
                DB::raw("COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count"),
                DB::raw("COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count"),
                DB::raw("COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count"),
                DB::raw("COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count"),
                DB::raw("COUNT(*) as total_orders")
            )
            ->first();

        // Product counts
        $newProducts = Product::where('is_new', true)->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        // Customer counts
        $totalCustomers = User::count();

        // This month's order count
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Calculate conversion rate (completed orders / total orders)
        $conversionRate = $orderStats->total_orders > 0
            ? ($orderStats->completed_count / $orderStats->total_orders) * 100
            : 0;

        return [
            Stat::make('Total Revenue', '₦' . number_format($orderStats->total_revenue ?? 0, 2))
                ->description('From paid & completed orders')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Monthly Revenue', '₦' . number_format($orderStats->monthly_revenue ?? 0, 2))
                ->description(now()->format('F Y') . ' sales')
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),

            Stat::make('Avg. Order Value', '₦' . number_format($orderStats->avg_order_value ?? 0, 2))
                ->description('Per paid/completed order')
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Pending Orders', number_format($orderStats->pending_count ?? 0))
                ->description('Awaiting payment')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->chart($this->getPendingChartData()),

            Stat::make('Paid Orders', number_format($orderStats->paid_count ?? 0))
                ->description('Ready for processing')
                ->color('info')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Completed Orders', number_format($orderStats->completed_count ?? 0))
                ->description('Successfully delivered')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Conversion Rate', number_format($conversionRate, 1) . '%')
                ->description('Order completion rate')
                ->color($conversionRate > 50 ? 'success' : 'warning')
                ->icon('heroicon-o-chart-bar'),
        ];
    }

    protected function getPendingChartData(): array
    {
        // Get last 7 days pending orders for chart
        $pendingData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Order::where('status', 'pending')
                ->whereDate('created_at', $date)
                ->count();
            $pendingData[] = $count;
        }

        return $pendingData;
    }
}
