<?php

namespace App\Filament\Widgets\TopStatistik;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RaihanDonasi extends ChartWidget
{
    protected static ?string $heading   = '';

    public static function canView(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || Auth::user()->profile?->group === 'donatur');
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins'   =>
            [
                'title' =>
                [
                    'display'   => true,
                    'text'      => 'Top 10 Donatur',
                    'font'      => ['size'  => 16],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user()?->profile;

        $query = DistribusiKencleng::query();

        $data = $query
            ->where('status', StatusDistribusi::DITERIMA)
            ->select('profiles.nama', DB::raw('SUM(distribusi_kenclengs.jumlah) as total_donasi'))
            ->join('profiles', 'distribusi_kenclengs.donatur_id', '=', 'profiles.id')
            ->groupBy('donatur_id', 'profiles.nama')
            ->orderBy('total_donasi', 'desc')
            ->limit(10)
            ->get();

        return
            [
                'datasets' => [
                    [
                        'axis'              => 'y',
                        'label'             => 'Top 10 Donatur',
                        'data'              => $data->map(fn($data) => $data->total_donasi)->toArray(),
                        'backgroundColor'   => '#5AB8F0',
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
