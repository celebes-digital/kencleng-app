<?php

namespace App\Filament\Pages\Koleksi;

use App\Filament\Widgets\JadwalKoleksiCalenderWidget;
use Filament\Pages\Page;

class KalenderKoleksi extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view               = 'filament.pages.koleksi.kalender-koleksi';
    protected static ?string $modelLabel        = 'Jadwal Koleksiki';
    protected static ?string $label             = 'Jadwal Koleksiki';
    protected static ?string $slug              = 'kalender/koleksi';

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
