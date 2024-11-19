<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\JadwalKoleksiCalenderWidget;
use Filament\Pages\Page;

class JadwalKoleksi extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view               = 'filament.pages.jadwal-koleksi';
    protected static ?string $modelLabel        = 'Jadwal Koleksiki';
    protected static ?string $label             = 'Jadwal Koleksiki';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'kalender-koleksi';
    protected static ?string $navigationGroup   = 'Jadwal';
    protected static ?int    $navigationSort    = 1;

    public static function getWidgets(): array
    {
        return [
            JadwalKoleksiCalenderWidget::class,
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            JadwalKoleksiCalenderWidget::class
        ];
    }

}
