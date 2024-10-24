<?php

namespace App\Filament\Widgets;

use App\Models\Infaq;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsPemasukanKenclengBulanan extends ChartWidget
{
    protected static ?string $heading = 'Statistik Pemasukan Kencleng Bulanan';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $data = Trend::model(Infaq::class)
            ->between(
                now()->startOfYear(),
                now()->endOfYear()
            )
            ->perMonth()
            ->sum('jumlah_donasi');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Kencleng',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'pointHoverRadius' => 2,
                    'tension' => 0.1
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}