<?php

namespace App\Filament\Resources\BatchKenclengResource\RelationManagers;

use App\Filament\Resources\BatchKenclengResource;
use App\Filament\Resources\KenclengResource;
use App\Models\BatchKencleng;
use App\Models\Kencleng;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Livewire\Component as Livewire;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class KenclengsRelationManager extends RelationManager
{
    protected static string $relationship   = 'kenclengs';
    protected static ?string $title         = 'Data Kencleng';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_kencleng')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return KenclengResource::table($table)
            ->recordTitleAttribute('no_kencleng')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Distribusi Kencleng')
                    ->button()
                    ->url(
                        fn (): string => 
                        BatchKenclengResource::getUrl('distribusi', ['record' => $this->ownerRecord->id])
                    ),
                Tables\Actions\Action::make('Tambah kencleng')
                    ->button()
                    ->requiresConfirmation()
                    ->action(function (Livewire $livewire) {
                        $no_kencleng = now()->timestamp;
                        $id_batch = $this->ownerRecord->id;

                        // Create a new Kencleng record
                        $kencleng = new \App\Models\Kencleng();
                        $kencleng->no_kencleng = $no_kencleng;
                        $kencleng->batch_kencleng_id = $id_batch;

                        // Generate QR code
                        $writer = new PngWriter();

                        // Create QR code
                        $qrCode = QrCode::create($no_kencleng)
                            ->setEncoding(new Encoding('UTF-8'))
                            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium)
                            ->setSize(290)
                            ->setMargin(5);
                        $result = $writer->write($qrCode);

                        // Save QR code to public storage
                        $filePath = 'public/qr-code/' . $no_kencleng . '.png';
                        Storage::disk('public')->put($filePath, $result->getString());

                        // Save file path to the database
                        $kencleng->qr_image = $filePath;
                        $kencleng->save();
                        $batch = BatchKencleng::find($kencleng->batch_kencleng_id)->first();

                        $batchKencleng = BatchKencleng::find($id_batch);
                        $batchKencleng->increment('jumlah');

                        $livewire->dispatch('refreshForm');
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Berhasil menambahkan kencleng'),
                    ),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->after(function (Kencleng $kencleng) {
                        $batchKencleng = BatchKencleng::find($kencleng->batch_kencleng_id);
                        $batchKencleng->decrement('jumlah');
                        $this->dispatch('refreshForm');
                    }),

                Tables\Actions\Action::make('download')
                    ->button()
                    ->action(function (Kencleng $kencleng) {
                        $filePath = $kencleng->qr_image;
                        if (Storage::disk('public')->exists($filePath)) {
                            return response()->download(Storage::disk('public')->path($filePath), 'kencleng-' . $kencleng->no_kencleng . '.png');
                        }
                        Notification::make()
                            ->title('Error')
                            ->body('File tidak ditemukan.')
                            ->send();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('QR Berhasil Diunduh')
                            ->body('QR Code telah berhasil diunduh.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
