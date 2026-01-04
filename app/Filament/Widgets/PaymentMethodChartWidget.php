<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentMethodChartWidget extends ChartWidget
{
    protected static ?int $sort = 6;

    public function getHeading(): string
    {
        return 'Payment Methods Distribution';
    }

    protected function getData(): array
    {
        $paymentMethods = Order::groupBy('payment_method')
            ->select('payment_method', DB::raw('count(*) as total'))
            ->get()
            ->pluck('total', 'payment_method')
            ->toArray();

        $methodLabels = [
            'paystack' => 'Paystack',
            'whatsapp' => 'WhatsApp',
        ];

        // Ensure all methods in database are accounted for
        $labels = [];
        $data = [];
        $colors = [
            '#3B82F6', // Paystack - blue
            '#10B981', // WhatsApp - green
        ];

        $colorIndex = 0;
        foreach ($paymentMethods as $method => $count) {
            $labels[] = $methodLabels[$method] ?? ucfirst($method);
            $data[] = $count;
            $colorIndex++;
        }

        // If no data, show empty state
        if (empty($data)) {
            $labels = ['Paystack', 'WhatsApp'];
            $data = [0, 0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders by Payment Method',
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
        return 'pie';
    }
}
