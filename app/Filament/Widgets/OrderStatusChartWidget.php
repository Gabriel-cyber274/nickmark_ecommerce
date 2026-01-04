<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Order Status Distribution';
    }

    protected function getData(): array
    {
        $statuses = Order::groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->orderBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $statusLabels = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $colors = [
            '#F59E0B', // Pending - amber
            '#3B82F6', // Paid - blue
            '#10B981', // Completed - green
            '#EF4444', // Cancelled - red
        ];

        // Ensure consistent order and include all statuses even if count is 0
        $labels = [];
        $data = [];
        $colorIndex = 0;

        foreach ($statusLabels as $key => $label) {
            $labels[] = $label;
            $data[] = $statuses[$key] ?? 0;
            $colorIndex++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders by Status',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
