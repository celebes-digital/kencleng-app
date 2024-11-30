<?php

namespace App\Filament\Widgets\TopStatistik;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RaihanKoleksi extends ChartWidget
{
    protected static ?string $heading   = '';

    public static function canView(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || Auth::user()->profile?->group === 'kolektor');
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins'   =>
            [
                'title' =>
                [
                    'display'   => true,
                    'text'      => 'Top 10 Kolektor',
                    'font'      => ['size'  => 16],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $query = DistribusiKencleng::query();

        $data = $query
            ->where('status', StatusDistribusi::DITERIMA)
            ->select('profiles.nama', DB::raw('COUNT(*) as total_koleksi'))
            ->join('profiles', 'distribusi_kenclengs.kolektor_id', '=', 'profiles.id')
            ->groupBy('kolektor_id', 'profiles.nama')
            ->orderBy('total_koleksi', 'desc')
            ->limit(10)
            ->get();

        return
            [
                'datasets' => [
                    [
                        'axis'              => 'y',
                        'label'             => 'Koleksi kencleng',
                        'data'              => $data->map(fn($data) => $data->total_koleksi)->toArray(),
                        'backgroundColor'   => '#F59E0B',
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
