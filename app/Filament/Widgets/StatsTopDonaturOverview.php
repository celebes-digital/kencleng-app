<?php

namespace App\Filament\Widgets;

use App\Models\Infaq;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsTopDonaturOverview extends ChartWidget
{
    protected static ?string $heading = '';

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Top 10 Donatur',
                    'font' => [
                        'size' => 16,
                    ],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $data = Infaq::whereNotNull('distribusi_id')
            ->join('distribusi_kenclengs', 'infaqs.distribusi_id', '=', 'distribusi_kenclengs.id')
            ->join('profiles', 'distribusi_kenclengs.donatur_id', '=', 'profiles.id')
            ->groupBy('profiles.nama')
            ->select('profiles.nama', DB::raw('SUM(infaqs.jumlah_donasi) as total_donasi'))
            ->orderByDesc('total_donasi')
            ->limit(10)
            ->get();

        return
        [
            'datasets' => [
                [
                    'axis'  => 'y',
                    'label' => 'Top 10 Donatur',
                    'data'  => $data->map(fn($data) => $data->total_donasi)->toArray(),
                    'backgroundColor'   
                            => [
                                '#36A2EB', // Original color
                                '#5AB8F0', // Original color
                                '#7FCDF5', // Original color
                                '#3B82F6', // Info color
                                '#60A5FA', // Info color
                                '#F59E0B', // Warning color
                                '#FBBF24', // Warning color
                            ],
                    'borderColor'       
                            => '#9BD0F5',
                ],
            ],
            'labels' => $data->map(fn ($data) => $data->nama)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected static ?array $options = ['indexAxis' => 'y',];
}
