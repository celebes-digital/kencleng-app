<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\DistribusiKencleng;
use App\Models\Infaq;
use App\Models\Profile;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsDashbordOverview extends BaseWidget
{
    private $startOfMonth;
    private $user;

    public function __construct()
    {
        $this->user         = Auth::user()->admin;
        $this->startOfMonth = Carbon::now()->startOfMonth();
    }

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }


    protected function getStats(): array
    {
        return [
            $this->getStatPemasukan(),
            $this->getStatDistribusi(),
            $this->getStatDonatur(),
        ];
    }

    private function getStatPemasukan()
    {
        $query      = Infaq::query();
        $query      = $this->filterData($query);

        $total      = $query->sum('jumlah_donasi');
        $bulanIni   = $query->where('tgl_transaksi', '>=', $this->startOfMonth)->sum('jumlah_donasi');

        return Stat::make('Total Pemasukan Kencleng', $this->formatRupiah($total))
            ->description($this->formatRupiah($bulanIni) . ' bulan ini')
            ->descriptionIcon('heroicon-m-banknotes')
            ->color('success');
    }

    private function getStatDistribusi()
    {
        $query      = DistribusiKencleng::query();
        $query      = $this->filterData($query);

        $total      = $query->get()->count();
        $bulanIni   = $query->where('tgl_distribusi', '>=', $this->startOfMonth)->count();

        return Stat::make('Total Distribusi Kencleng', $total . ' Kencleng')
            ->description($bulanIni . ' kencleng bulan ini')
            ->descriptionIcon('heroicon-m-cube')
            ->color('info');
    }

    private function getStatDonatur()
    {
        $query      = Profile::where('group', 'donatur');
        $query      = $this->filterData($query);

        $total      = $query->count();
        $bulanIni   = $query->where('created_at', '>=', $this->startOfMonth)->count();

        return Stat::make('Total Donatur', $total . ' Donatur')
            ->description($bulanIni . ' donatur bulan ini')
            ->descriptionIcon('heroicon-m-user-plus')
            ->color('warning');
    }

    private function filterData($query)
    {
        if($this->user->level === 'supervisor') 
            return $query->where('area_id', $this->user->area_id);

        if($this->user->level === 'admin' || $this->user->level === 'manajer') 
            return $query->where('cabang_id', $this->user->cabang_id);

        if($this->user->level === 'admin_wilayah' || $this->user->level === 'direktur') 
            return $query->where('wilayah_id', $this->user->wilayah_id);
        
        return $query;
    }

    // Fungsi format ke uang, jadi kalau 1980200 menjadi Rp 1,98 Jt
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
