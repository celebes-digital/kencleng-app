<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MonthlyStatistik\RaihanPengguna;
use App\Filament\Widgets\StatsDashbordOverview;
use App\Filament\Widgets\StatsPemasukanKenclengBulanan;
use App\Filament\Widgets\TableWidgets\KenclengTerbaru;
use App\Filament\Widgets\TopStatistik\RaihanDistribusi;
use App\Filament\Widgets\TopStatistik\RaihanDonasi;
use App\Filament\Widgets\TopStatistik\RaihanKoleksi;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = 'Dashboard';
    protected static ?string $modelLabel        = 'Dashboard';
    protected static ?string $label             = 'Dashboard';
    protected static ?string $navigationIcon    = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            StatsDashbordOverview::class,
            StatsPemasukanKenclengBulanan::class,
            RaihanDonasi::class,
            RaihanDistribusi::class,
            RaihanKoleksi::class,
            RaihanPengguna::class,
            KenclengTerbaru::class,
        ];
    }
}
