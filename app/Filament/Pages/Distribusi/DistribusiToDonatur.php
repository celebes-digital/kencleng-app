<?php

namespace App\Filament\Pages\Distribusi;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\Auth;

class DistribusiToDonatur extends Page
{
    protected static string $view   = 'filament.pages.distribusi.to-donatur';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user->is_admin && $user->admin->level === 'admin';
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?string $navigationLabel   = 'Ke Donatur';
    protected static ?string $modelLabel        = 'Ke Donatur';
    protected static ?string $title             = 'Ke Donatur';
    protected static ?string $slug              = 'distribusi/donatur';

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->validateResult(false)
            ->data(url('daftar/donatur/' . $user->admin->cabang_id))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin);

        $result = $builder->build();
        $qrCode = $result->getDataUri();

        return [
            Action::make('open_qr')
            ->label('QR Pendaftaran')
            ->icon('heroicon-o-viewfinder-circle')
            ->modalContent(view('filament.components.show-qr', [
                'title'     => 'Donatur',
                'qr'        => $qrCode,
                'cabang'    => $user->admin->cabang->nama_cabang ?? 'The Best Cabang'
            ]))
            ->modal()
            ->modalHeading('Pendaftaran Donatur')
            ->modalSubmitAction(false)
        ];
    }
}
