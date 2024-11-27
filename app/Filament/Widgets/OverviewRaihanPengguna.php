<?php

namespace App\Filament\Widgets;

use App\Models\DistribusiKencleng;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OverviewRaihanPengguna extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return !Auth::user()->is_admin;
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $user = filament()->auth()->user();

        $query = DistribusiKencleng::query();

        if( $user->profile->group === 'donatur' )
        {
            $lable = 'Total Raihan Donasi';
            $data = $query->where('donatur_id', $user->profile->id)->sum('jumlah');
            $data = 'IDR ' . number_format($data, thousands_separator: '.', decimal_separator: ',');
        }

        if( $user->profile->group === 'distributor' )
        {
            $lable = 'Total Kencleng Didistribusi';
            $data = $query->where('distributor_id', $user->profile->id)->count();
            $data = $data . ' kencleng';
        }

        if ( $user->profile->group === 'kolektor' )
        {
            $lable = 'Total Kencleng Dikoleksi';
            $data = $query->where('kolektor_id', $user->profile->id)->count();
            $data = $data . ' kencleng';
        }

        return [
            Stat::make(
                'Selamat datang di dashboard Kencleng Jariyah', 
                filament()->getUserName(filament()->auth()->user()))
                ->extraAttributes([
                    'class' => 'hover:scale-[1.02] transition-transform duration-300',
                ]),
            Stat::make($lable, $data)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
                ->extraAttributes([
                    'class' => 'hover:scale-[1.005] transition-transform duration-300',
                ]),
        ];
    }
}
