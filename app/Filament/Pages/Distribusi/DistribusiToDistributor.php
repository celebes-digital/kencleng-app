<?php

namespace App\Filament\Pages\Distribusi;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Illuminate\Support\Facades\Auth;

class DistribusiToDistributor extends Page
{
    protected static string $view   = 'filament.pages.distribusi.to-distributor';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user->is_admin && $user->admin->level === 'admin';
    }

    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?string $navigationLabel   = 'Ke Distributor';
    protected static ?string $modelLabel        = 'Ke Distributor';
    protected static ?string $title             = 'Ke Distributor';
    protected static ?string $slug              = 'distribusi/distributor';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_qr')
            ->label('QR Pendaftaran')
            ->icon('heroicon-o-viewfinder-circle')
            ->modal()
        ];
    }
}
