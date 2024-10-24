<?php

namespace App\Filament\Resources;

use App\Filament\Components\ScannerQrCode;
use App\Filament\Resources\DistribusiKenclengResource\Pages;
use App\Models\DistribusiKencleng;
use App\Models\Infaq;
use App\Models\Kencleng;
use App\Models\Profile;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class DistribusiKenclengResource extends Resource
{
    protected static ?string $model = DistribusiKencleng::class;

    protected static ?string $modelLabel        = 'Data Distribusi';
    protected static ?string $label             = 'Data Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'distribusi-kencleng';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    ScannerQrCode::make('scanner')
                        ->live()
                        ->afterStateUpdated(
                            function (Set $set, $state) 
                            {
                                $kencleng = Kencleng::where('no_kencleng', $state)->first();

                                Notification::make()
                                    ->title(
                                        'Kencleng ' 
                                            . $kencleng->no_kencleng  
                                            . ' ditemukan'
                                    )
                                    ->success()
                                    ->send();

                                $set('kencleng_id', $kencleng->id);
                            }
                    ),

                    Fieldset::make('Data Kencleng')
                        ->schema([
                            Select::make('kencleng_id')
                                ->label('No. Kencleng')
                                ->options(Kencleng::all()->pluck('no_kencleng', 'id'))
                                ->searchable()
                                ->required(),

                            Toggle::make('tag_lokasi')
                                ->label('Tag Lokasi')
                                ->inline(false)
                                ->extraAttributes(['class' => 'mt-2'])
                                ->helperText('Aktifkan untuk menandai lokasi saat ini'),

                            Select::make('donatur_id')
                                ->label('Donator')
                                ->options(
                                    Profile::where('group', 'donatur')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable(),

                            Select::make('distributor_id')
                                ->label('Distributor')
                                ->options(
                                    Profile::where('group', 'distributor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable(),

                            Select::make('kolektor_id')
                                ->label('Kolektor')
                                ->options(
                                    Profile::where('group', 'kolektor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable(),
                        ])
                        ->columns(1),
                ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('No. Kencleng')
                    ->sortable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor.nama')
                    ->label('Distributor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('geo_lat')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('geo_long')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('tgl_distribusi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_pengambilan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('diterima')
                    ->boolean()
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('konfirmasi')
                    ->button()
                    ->color(
                        fn ($record) 
                            => $record->tgl_pengambilan && !$record->diterima
                                ? 'primary' 
                                : 'gray' 
                    )
                    ->disabled(
                        fn ($record) 
                            => !$record->tgl_pengambilan || $record->diterima
                    )
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->form([
                        TextInput::make('jumlah_donasi')
                            ->label('Jumlah Diterima')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->required(),
                        Textarea::make('uraian')
                            ->autosize()
                            ->rows(3)
                    ])
                    ->action(
                        function (DistribusiKencleng $record, $data) {
                            Infaq::create([
                                'distribusi_id' => $record->id,
                                'tgl_transaksi' => now(),
                                'jumlah_donasi' => $data['jumlah_donasi'],
                                'uraian'        => $data['uraian'],
                            ]);
                            $record->update([
                                'diterima' => true,
                            ]);
                        }
                    ),
                Tables\Actions\Action::make('lokasi')
                    ->iconButton()
                    ->tooltip('Lihat Lokasi')
                    ->icon('heroicon-o-map-pin')
                    ->color(
                        fn ($record) 
                                => ($record->geo_lat && $record->geo_long)
                                    ? 'info'
                                    : 'gray'
                    )
                    ->disabled(
                        fn ($record) 
                                => !($record->geo_lat && $record->geo_long)
                    )
                    ->url(
                        fn ($record) 
                                => "https://www.google.com/maps/search/?api=1&query=" 
                                    . $record->geo_lat
                                    . "," 
                                    . $record->geo_long, 
                        true
                    ),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistribusiKenclengs::route('/'),
            'create' => Pages\CreateDistribusiKencleng::route('/create'),
            'edit' => Pages\EditDistribusiKencleng::route('/{record}/edit'),
        ];
    }
}
