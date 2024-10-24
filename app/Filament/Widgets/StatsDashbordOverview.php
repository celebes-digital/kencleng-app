<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\DistribusiKencleng;
use App\Models\Infaq;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class StatsDashbordOverview extends BaseWidget
{

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }

    protected function getStats(): array
    {
        $now                = Carbon::now();
        $startOfMonth       = $now->startOfMonth();

        $totalPemasukanKencleng         = Infaq::sum('jumlah_donasi');

        $totalDistribusiKencleng        = DistribusiKencleng::whereNotNull('tgl_distribusi')
                                                            ->count();

        $distribusiKenclengBulanIni     = DistribusiKencleng::where
                                            (
                                                'tgl_distribusi', '>=', $now->startOfMonth()
                                            )
                                            ->count();
        
        $totalPengguna                  = Profile::all()->count();
        
        $totalPenggunaBulanIni          = Profile::where
                                            (
                                                'created_at', '>=', $now->startOfMonth()
                                            )
                                            ->count();


        $currentMonthSum                = Infaq::where
                                            (
                                                'tgl_transaksi', '>=', $startOfMonth
                                            )
                                            ->sum('jumlah_donasi');

        return [
            Stat::make
                (
                    'Total Pemasukan Kencleng', 
                    $this->formatRupiah($totalPemasukanKencleng)
                )
                ->description
                (
                    $this->formatRupiah($currentMonthSum) . ' bulan ini'
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make
                (
                    'Total Distribusi Kencleng', 
                    $totalDistribusiKencleng . ' Kencleng'
                )
                ->description($distribusiKenclengBulanIni . ' kencleng bulan ini')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
                
            Stat::make
                (
                    'Total Pengguna', 
                    $totalPengguna . ' Pengguna'
                )
                ->description($totalPenggunaBulanIni . ' pengguna bulan ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
        ];
    }


    // Fungsi format ke uang, jadi kalau satu 1980200 menjadi Rp 1,98 Jt
    private function formatRupiah($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp'
                . number_format($amount / 1000000000, 2)
                . ' M';
        } elseif ($amount >= 1000000) {
            return 'Rp'
                . number_format($amount / 1000000, 1)
                . ' Jt';
        } elseif ($amount >= 1000) {
            return 'Rp'
                . number_format($amount / 1000)
                . ' Rb';
        } else {
            return 'Rp ' . number_format($amount, 2);
        }
    }
}
