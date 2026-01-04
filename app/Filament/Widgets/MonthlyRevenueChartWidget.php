<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class MonthlyRevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Monthly Revenue Trend';
    }

    protected function getData(): array
    {
        $data = Trend::model(Order::class)
            ->between(
                start: now()->subMonths(11)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->sum('total');

        // Debug output - remove after testing
        // \Log::info('Trend Data:', $data->toArray());

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $data->map(fn(TrendValue $value) => (float) $value->aggregate),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension'  => 0.4,
                ],
            ],
            'labels' => $data->map(function (TrendValue $value) {
                // Handle both string and Carbon dates
                if ($value->date instanceof \Carbon\Carbon) {
                    return $value->date->format('M Y');
                }

                // If it's a string, parse it
                return Carbon::parse($value->date)->format('M Y');
            }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
