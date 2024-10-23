<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatsTopDonaturOverview extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = DB::table('distribusi_kenclengs')
                    ->join(
                        'profiles', 'distribusi_kenclengs.donatur_id', '=', 'profiles.id'
                    )
                    ->select(
                        'donatur_id' ,
                        'profiles.nama', 
                        DB::raw('sum(jumlah) as total_donasi')
                    )
                    ->groupBy('donatur_id')
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
                    'backgroundColor'   => '#36A2EB',
                    'borderColor'       => '#9BD0F5',
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
