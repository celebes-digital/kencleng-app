<?php

namespace App\Filament\Widgets;

use App\Models\Infaq;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsTopDonaturOverview extends ChartWidget
{
    protected static ?string $heading = 'Top 7 donatur';

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }

    protected function getData(): array
    {
        $data = Infaq::query()
                ->whereNotNull('distribusi_id')
                ->join('distribusi_kenclengs', 'infaqs.distribusi_id', '=', 'distribusi_kenclengs.id')
                ->join('profiles', 'distribusi_kenclengs.donatur_id', '=', 'profiles.id')
                ->groupBy('distribusi_kenclengs.donatur_id')
                ->select('profiles.nama', DB::raw('sum(infaqs.jumlah_donasi) as total_donasi'))
                ->orderBy('total_donasi', 'desc')
                ->limit(7)
                ->get();

        return
        [
            'datasets' => [
                [
                    'axis'  => 'y',
                    'label' => 'Top 7 Donatur',
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
