<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KenclengResource\Pages;
use App\Models\DistribusiKencleng;
use App\Models\Kencleng;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class KenclengResource extends Resource
{
    protected static ?string $model             = Kencleng::class;
    protected static ?string $label             = 'Data Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-arrow-down';
    protected static ?string $slug              = 'kencleng';
    protected static ?string $navigationGroup   = 'Kencleng';
    protected static ?int    $navigationSort    = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('batch_kencleng_id')
                    ->label('Batch ke')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('no_kencleng')
                    ->required()
                    ->maxLength(10),
                Forms\Components\FileUpload::make('qr_image')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Cek status terkahir kencleng
        // Jika ada dan tgl_pengambilan ada, maka status = 'Tersedia'
        // Jika tgl_distribusi ada dan tgl_pengambilan null, maka status = 'Sedang diisi'
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_kencleng_id')
                    ->label('Batch ke')
                    ->numeric()
                    ->grow(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_kencleng')
                    ->grow(false)
                    ->extraAttributes(['class' => 'font-bold text-lg text-blue-500'])
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qr_image')
                    ->size('150px')
                    ->alignCenter()
                    ->square(),
                Tables\Columns\TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'Tersedia' => 'success',
                            'Sedang diisi' => 'warning',
                            'Belum didistribusikan' => 'danger',
                        })
                        ->getStateUsing(function (Kencleng $record) {
                                $latestDistribusi = $record
                                                        ->distribusiKenclengs()
                                                        ->latest('tgl_distribusi')
                                                        ->first();

                                if ($latestDistribusi) {
                                    if ($latestDistribusi->tgl_pengambilan) {
                                        return 'Tersedia';
                                    } elseif ($latestDistribusi->tgl_distribusi && !$latestDistribusi->tgl_pengambilan) {
                                        return 'Sedang diisi';
                                    }
                                }
                                return 'Belum didistribusikan';
                            })
                        ])
            ->defaultSortOptionLabel('batch_kencleng_id.nama_batch', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_riwayat')
                    ->label('Riwayat')
                    ->url(fn($record) => KenclengResource::getUrl('riwayat', ['record' => $record])),
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
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKenclengs::route('/'),
            'create' => Pages\CreateKencleng::route('/create'),
            'riwayat' => Pages\RiwayatKencleng::route('/{record}/riwayat'),
        ];
    }
}
