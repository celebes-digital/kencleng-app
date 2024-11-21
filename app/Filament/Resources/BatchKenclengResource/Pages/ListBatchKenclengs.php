<?php

namespace App\Filament\Resources\BatchKenclengResource\Pages;

use App\Filament\Resources\BatchKenclengResource;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ListBatchKenclengs extends ListRecords
{
    protected static string $resource = BatchKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Batch')
                ->createAnother(false)
                ->using(function(array $data, string $model): Model {
                    $batch = $model::create($data);

                    for ($i = 0; $i < $data['jumlah']; $i++) {
                        $no_kencleng = now()->timestamp + $i;

                        // Create a new Kencleng record
                        $kencleng = new \App\Models\Kencleng();
                        $kencleng->no_kencleng          = $no_kencleng;
                        $kencleng->batch_kencleng_id    = $batch->id;

                        // Generate QR code
                        $writer = new PngWriter();

                        // Create QR code
                        $qrCode = QrCode::create($no_kencleng)
                            ->setEncoding(new Encoding('UTF-8'))
                            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
                            ->setSize(290)
                            ->setMargin(5);
                        $result = $writer->write($qrCode);

                        // Save QR code to storage
                        $filePath = 'public/qr-code/' . $no_kencleng . '.png';
                        Storage::disk('public')->put($filePath, $result->getString());

                        // Save file path to the database
                        $kencleng->qr_image = $filePath;
                        $kencleng->save();
                    }
                    return $batch;
                }),
        ];
    }
}
