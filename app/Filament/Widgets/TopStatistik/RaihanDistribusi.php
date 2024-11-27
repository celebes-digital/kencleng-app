<?php

namespace App\Filament\Widgets\TopStatistik;

use App\Models\DistribusiKencleng;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RaihanDistribusi extends ChartWidget
{
    protected static ?string $heading   = '';

    public static function canView(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || Auth::user()->profile?->group === 'distributor');
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins'   =>
            [
                'title' =>
                [
                    'display'   => true,
                    'text'      => 'Top 10 Distributor',
                    'font'      => ['size'  => 16],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $query = DistribusiKencleng::query();

        $data = $query
            ->select('profiles.nama', DB::raw('COUNT(*) as total_distribusi'))
            ->join('profiles', 'distribusi_kenclengs.distributor_id', '=', 'profiles.id')
            ->groupBy('distributor_id', 'profiles.nama')
            ->orderBy('total_distribusi', 'desc')
            ->limit(10)
            ->get();

        return
            [
                'datasets' => [
                    [
                        'axis'              => 'y',
                        'label'             => 'Top 10 Distributor',
                        'data'              => $data->map(fn($data) => $data->total_distribusi)->toArray(),
                        'backgroundColor'   => '#3B82F6',
                        'borderColor'       => '#9BD0F5',
                    ],
                ],
                'labels' => $data->map(fn($data) => $data->nama)->toArray(),
            ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
