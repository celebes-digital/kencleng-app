<?php

namespace App\Filament\Resources;

use App\Enums\StatusKencleng;
use App\Filament\Resources\KenclengResource\Pages;
use App\Models\Kencleng;
use Filament\Forms;
use Filament\Forms\Form;
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_kencleng_id')
                    ->label('Batch')
                    ->grow(false)
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('qr_image')
                    ->size('120px')
                    ->grow(false)
                    ->square(),
                Tables\Columns\TextColumn::make('no_kencleng')
                    ->grow(false)
                    ->extraAttributes(['class' => 'font-bold text-lg text-blue-500'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(StatusKencleng::class)
            ])
            ->defaultSortOptionLabel('batch_kencleng_id.nama_batch', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('view_riwayat')
                    ->label('Riwayat')
                    ->icon('heroicon-o-film')
                    ->url(
                        fn($record)
                        => KenclengResource::getUrl(
                            'riwayat',
                            ['record' => $record]
                        )
                    ),
                Tables\Actions\Action::make('download')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(
                        function (Kencleng $kencleng) {
                            $filePath = $kencleng->qr_image;
                            if (Storage::disk('public')->exists($filePath)) {
                                return response()
                                    ->download(
                                        Storage::disk('public')
                                            ->path(
                                                $filePath
                                            ),
                                        'kencleng-'
                                            . $kencleng->no_kencleng
                                            . '.png'
                                    );
                            }

                            Notification::make()
                                ->title('Error')
                                ->icon('heroicon-o-exclamation-circle')
                                ->iconColor('danger')
                                ->body('File tidak ditemukan.')
                                ->send();
                        }
                    )
                    ->successNotification(
                        Notification::make()
                            ->title('QR Berhasil Diunduh')
                            ->body('QR Code telah berhasil diunduh.')
                    ),

            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index'     => Pages\ListKenclengs::route('/'),
            'create'    => Pages\CreateKencleng::route('/create'),
            'riwayat'   => Pages\RiwayatKencleng::route('/{record}/riwayat'),
        ];
    }
}
