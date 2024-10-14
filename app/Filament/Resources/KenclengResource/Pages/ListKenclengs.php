<?php

namespace App\Filament\Resources\KenclengResource\Pages;

use App\Filament\Resources\KenclengResource;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\ListRecords;

class ListKenclengs extends ListRecords
{
    protected static string $resource = KenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Tambah satu kencleng')
                ->button()
                ->action(function () {
                    $no_kencleng = now()->timestamp;

                    // Create a new Kencleng record
                    $kencleng = new \App\Models\Kencleng();
                    $kencleng->no_kencleng = $no_kencleng;

                    // Generate QR code
                    $writer = new PngWriter();

                    // Create QR code
                    $qrCode = QrCode::create('Life is too short to be generating QR codes')
                        ->setEncoding(new Encoding('UTF-8'))
                        ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
                        ->setSize(300)
                        ->setMargin(10);
                    $result = $writer->write($qrCode);

                    // Save QR code to storage
                    $filePath = $no_kencleng . '.png';
                    Storage::put($filePath, $result->getString());

                    // Save file path to the database
                    $kencleng->qr_image = $filePath;
                    $kencleng->save();
                })
                ->successNotification(
                    Notification::make()
                                ->success()
                                ->title('User registered')
                                ->body('The user has been created successfully.'),
                ),
            Action::make('addBulkKencleng')
                ->form([
                    TextInput::make('jumlah_kencleng')
                        ->label('Jumlah Kencleng')
                        ->required()
                        ->rules('numeric'),
                ])
                ->action(function (array $data) {
                    $jumlah_kencleng = $data['jumlah_kencleng'];

                    for ($i = 0; $i < $jumlah_kencleng; $i++) {
                        $no_kencleng = now()->timestamp + $i;

                        // Create a new Kencleng record
                        $kencleng = new \App\Models\Kencleng();
                        $kencleng->no_kencleng = $no_kencleng;

                        // Generate QR code
                        $writer = new PngWriter();

                        // Create QR code
                        $qrCode = QrCode::create('Life is too short to be generating QR codes')
                            ->setEncoding(new Encoding('UTF-8'))
                            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
                            ->setSize(300)
                            ->setMargin(10);
                        $result = $writer->write($qrCode);

                        // Save QR code to storage
                        $filePath = $no_kencleng . '.png';
                        Storage::put($filePath, $result->getString());

                        // Save file path to the database
                        $kencleng->qr_image = $filePath;
                        $kencleng->save();
                    }
                })
        ];
    }
}
