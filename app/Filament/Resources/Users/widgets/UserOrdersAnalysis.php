<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOrdersAnalysis extends BaseWidget
{
    public User $record;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $orders = $this->record->orders()->get();

        // Categorize orders
        $validOrders = $orders->whereNotIn('status', ['pending', 'cancelled']); // completed/paid
        $pendingOrders = $orders->where('status', 'pending');
        $cancelledOrders = $orders->where('status', 'cancelled');

        // Totals
        $totalRevenue = $validOrders->sum('total');
        $completedRevenue = $validOrders->sum('total');
        $pendingRevenue = $pendingOrders->sum('total');
        $cancelledRevenue = $cancelledOrders->sum('total');

        // Counts
        $totalCount = $orders->count();
        $completedCount = $validOrders->count();
        $pendingCount = $pendingOrders->count();
        $cancelledCount = $cancelledOrders->count();

        // Averages
        $averageOrderValue = $totalCount > 0 ? $orders->sum('total') / $totalCount : 0;
        $averageValid = $completedCount > 0 ? $completedRevenue / $completedCount : 0;
        $averagePending = $pendingCount > 0 ? $pendingRevenue / $pendingCount : 0;
        $averageCancelled = $cancelledCount > 0 ? $cancelledRevenue / $cancelledCount : 0;

        // Chart data for progress bars (0–100 scale)
        $chartDataTotal = $this->generateChart($totalRevenue, max($totalRevenue, 100));
        $chartDataCompleted = $this->generateChart($completedRevenue, max($completedRevenue, 100));
        $chartDataPending = $this->generateChart($pendingRevenue, max($pendingRevenue, 100));
        $chartDataCancelled = $this->generateChart($cancelledRevenue, max($cancelledRevenue, 100));

        return [
            Stat::make('Total Revenue', "₦" . number_format($totalRevenue, 2))
                ->description("Orders: {$totalCount}, Avg: ₦" . number_format($averageOrderValue, 2))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary')
                ->chart($chartDataTotal)
                ->chartColor('primary'),

            Stat::make('Completed Revenue', "₦" . number_format($completedRevenue, 2))
                ->description("Orders: {$completedCount}, Avg: ₦" . number_format($averageValid, 2))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($chartDataCompleted)
                ->chartColor('success'),

            Stat::make('Pending Revenue', "₦" . number_format($pendingRevenue, 2))
                ->description("Orders: {$pendingCount}, Avg: ₦" . number_format($averagePending, 2))
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart($chartDataPending)
                ->chartColor('warning'),

            Stat::make('Cancelled Revenue', "₦" . number_format($cancelledRevenue, 2))
                ->description("Orders: {$cancelledCount}, Avg: ₦" . number_format($averageCancelled, 2))
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->chart($chartDataCancelled)
                ->chartColor('danger'),
        ];
    }

    // Helper to generate simple progress data
    private function generateChart(float $value, float $max): array
    {
        $percentage = $max > 0 ? ($value / $max) * 100 : 0;
        $percentage = min($percentage, 100);

        $chartData = [];
        for ($i = 0; $i <= 100; $i += 10) {
            $chartData[] = $i <= $percentage ? $percentage : 0;
        }

        return $chartData;
    }
}
