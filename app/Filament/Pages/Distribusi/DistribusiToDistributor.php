<?php

namespace App\Filament\Pages\Distribusi;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
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
        $user = Auth::user();

        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->validateResult(false)
            ->data(url('daftar/distributor/'. $user->admin->cabang_id))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin);
            // ->labelText($user->admin->cabang->nama_cabang ?? 'The Best Cabang')
            // ->labelFont(new OpenSans(20))
            // ->labelAlignment(LabelAlignment::Center);

        $result = $builder->build();
        $qrCode = $result->getDataUri();

        return [
            Action::make('open_qr')
            ->label('QR Pendaftaran')
            ->icon('heroicon-o-viewfinder-circle')
            ->modalContent(view('filament.components.show-qr', [
                'title'     => 'Distributor',
                'qr'        => $qrCode,
                'cabang'    => $user->admin->cabang->nama_cabang ?? 'The Best Cabang'
            ]))
            ->modal()
            ->modalHeading('Pendaftaran Distributor')
            ->modalSubmitAction(false)
        ];
    }
}
