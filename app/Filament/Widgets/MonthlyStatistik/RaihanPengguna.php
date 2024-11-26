<?php

namespace App\Filament\Widgets\MonthlyStatistik;

use App\Models\DistribusiKencleng;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use Illuminate\Support\Facades\Auth;

class RaihanPengguna extends ChartWidget
{
    protected static ?string $heading = '';

    public static function canView(): bool
    {
        return !Auth::user()->is_admin;
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Perolehan Kencleng Bulanan',
                    'font' => [
                        'size' => 16,
                    ],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $query  = $this->getQuery();
        $data   = Trend::model(DistribusiKencleng::class)
                    ->query($query)
                    ->between(now()->startOfYear(), now()->endOfYear())
                    ->perMonth()
                    ->sum('jumlah');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Kencleng',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'pointHoverRadius' => 2,
                    'tension' => 0.1
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    private function getQuery()
    {
        $user = Auth::user()->profile;

        if ($user?->group === 'donatur') {
            return DistribusiKencleng::query()->where('donatur_id', $user->id);
        }

        if ($user?->group === 'distributor') {
            return DistribusiKencleng::query()->where('distributor_id', $user->id);
        }

        if ($user?->group === 'kolektor') {
            return DistribusiKencleng::query()->where('kolektor_id', $user->id);
        }

        return DistribusiKencleng::query();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
