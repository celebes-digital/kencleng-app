<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\DistribusiKencleng;

class StatsDashbordOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();

        $startOfMonth       = $now->startOfMonth();
        $startOfLastMonth   = $now->subMonth()->startOfMonth();
        $endOfLastMonth     = $now->subMonth()->endOfMonth();

        $currentMonthSum    = DistribusiKencleng::where
                                (
                                    'tgl_pengambilan', '>=', $startOfMonth
                                )
                                ->sum('jumlah');
        $lastMonthSum       = DistribusiKencleng::whereBetween
                                (
                                    'tgl_pengambilan',
                                    [$startOfLastMonth, $endOfLastMonth]
                                )
                                ->sum('jumlah');

        $percentageChange   = $lastMonthSum > 0
                                ? (($currentMonthSum - $lastMonthSum) / $lastMonthSum) * 100
                                : 0;

        $description        = $percentageChange >= 0
                                ? abs($percentageChange) . '% meningkat'
                                : abs($percentageChange) . '% menurun';
        $descriptionIcon    = $percentageChange >= 0
                                ? 'heroicon-m-arrow-trending-up'
                                : 'heroicon-m-arrow-trending-down';

        $currentMonthCount  = DistribusiKencleng::where
                                (
                                    'tgl_pengambilan', '>=', $startOfMonth
                                )
                                ->count();

        $averageDailyDistribusi     = $currentMonthCount > 0
                                        ? $currentMonthSum / $currentMonthCount
                                        : 0;
        
        // Perhitungan jumlah kencleng masuk
        $currentMonthKenclengMasuk  = DistribusiKencleng::where
                                        (
                                            'tgl_pengambilan', '>=', $startOfMonth
                                        )
                                        ->count();
        $lastMonthKenclengMasuk     = DistribusiKencleng::whereBetween
                                        (
                                            'tgl_pengambilan',
                                            [$startOfLastMonth, $endOfLastMonth]
                                        )
                                        ->count();

        $kenclengMasukDifference    = $lastMonthKenclengMasuk > 0
                                        ? (($currentMonthKenclengMasuk - $lastMonthKenclengMasuk) / $lastMonthKenclengMasuk) * 100
                                        : 0;

        $kenclengMasukDescription   = $kenclengMasukDifference >= 0
                                        ? 'Bertambah ' . abs($kenclengMasukDifference)
                                        : 'Berkurang ' . abs($kenclengMasukDifference);
        $isMeningkat                = $kenclengMasukDifference >= 0
                                        ? true
                                        : false;
        
        // Perhitungan pengguna yang mendaftar
        $allUsers               = \App\Models\Profile::all()->count();
        $currentMonthNewUsers   = \App\Models\Profile::where
                                    (
                                        'created_at', '>=', $startOfMonth
                                    )
                                    ->count();

        return [
            Stat::make('Pemasukan Kencleng', 'Rp ' . $currentMonthSum)
                ->description($description . ' dari bulan lalu')
                ->descriptionIcon($descriptionIcon)
                ->color('success'),

            Stat::make('Kencleng Masuk', $currentMonthCount . ' Kencleng')
                ->description($kenclengMasukDescription . ' dari bulan lalu')
                ->descriptionIcon(
                    'heroicon-m-arrow-trending-' . ($isMeningkat ? 'up' : 'down')
                )
                ->color($isMeningkat ? 'success' : 'danger'),
                
            Stat::make('Pengguna Baru', $allUsers . ' Pengguna')
                ->description('Bertambah ' . $currentMonthNewUsers . ' pengguna bulan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }
}
