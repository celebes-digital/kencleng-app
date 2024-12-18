<?php

namespace App\Filament\Resources;

use App\Enums\StatusDistribusi;
use App\Filament\Components\ScannerQrCode;
use App\Filament\Resources\DistribusiKenclengResource\Pages;
use App\Models\DistribusiKencleng;
use App\Models\Infaq;
use App\Models\Kencleng;
use App\Models\Profile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class DistribusiKenclengResource extends Resource
{
    protected static ?string $model = DistribusiKencleng::class;

    protected static ?string $modelLabel        = 'Data Distribusi';
    protected static ?string $label             = 'Data Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'distribusi';
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
                            function (Set $set, $state) {
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
                                ->relationship('kencleng', 'no_kencleng')
                                ->options(Kencleng::all()->pluck('no_kencleng', 'id'))
                                ->searchable()
                                ->required()
                                ->optionsLimit(10),


                            Select::make('distributor_id')
                                ->label('Distributor')
                                ->relationship('distributor', 'nama')
                                ->options(
                                    Profile::where('group', 'distributor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->required()
                                ->optionsLimit(10),

                            Select::make('donatur_id')
                                ->label('Donator')
                                ->relationship('donatur', 'nama')
                                ->options(
                                    Profile::where('group', 'donatur')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->optionsLimit(10),

                            Select::make('kolektor_id')
                                ->label('Kolektor')
                                ->relationship('kolektor', 'nama')
                                ->options(
                                    Profile::where('group', 'kolektor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->optionsLimit(10),
                        ])
                        ->columns(1),
                ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if(!$user->is_admin) {
                    $query->where('distributor_id', $user->profile->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.nama_area')
                    ->label('Area')
                    ->placeholder('Beludm ada')
                    ->hidden(fn($livewire) => $livewire->activeTab === 'Distribusi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->placeholder('Belum ada')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->placeholder('Belum ada')
                    ->hidden(fn($livewire) => $livewire->activeTab === 'Distribusi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor.nama')
                    ->label('Distributor')
                    ->placeholder('Belum ada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_lat')
                    ->label('Latitude')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_long')
                    ->label('Longitude')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_distribusi')
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'Distribusi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_aktivasi')
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'Sedang Diisi')
                    ->date('d F Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_pengambilan')
                    ->label('Tanggal Pengambilan')
                    ->hidden(fn($livewire) => $livewire->activeTab !== 'Kembali')
                    ->date('d F Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_batas_pengambilan')
                    ->label('Tanggal Pengambilan')
                    ->hidden(fn($livewire) => $livewire->activeTab !== 'Sedang Diisi')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'Diterima')
                    ->prefix('IDR ')
                    ->numeric(thousandsSeparator: '.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge(StatusDistribusi::class)
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([])
            ->actions([
                // Tables\Actions\Action::make('konfirmasi')
                //     ->button()
                //     ->hidden(
                //         fn($record)
                //         => $record->status->value !== 'kembali')
                //     ->color(
                //         fn($record)
                //             => $record->status->value === 'distribusi'
                //             ? 'primary'
                //             : 'gray'
                //     )
                //     ->disabled(
                //         fn($record)
                //         => $record->status->value !== 'kembali'
                //     )
                //     ->modalSubmitActionLabel('Konfirmasi')
                //     ->form(fn($record) => [
                //         TextInput::make('donasi')
                //             ->default(Number::currency($record->jumlah, 'IDR', 'id'))
                //             ->prefixIcon('heroicon-o-banknotes')
                //             ->disabled(),
                //         TextInput::make('jumlah_donasi')
                //             ->label('Jumlah Diterima')
                //             ->prefix('Rp')
                //             ->numeric()
                //             ->minValue(0)
                //             ->required(),
                //         Textarea::make('uraian')
                //             ->label('Keterangan Tambahan')
                //             ->autosize()
                //             ->rows(3)
                //     ])
                //     ->action(
                //         function (DistribusiKencleng $record, $data) {
                //             Infaq::create([
                //                 'distribusi_id' => $record->id,
                //                 'tgl_transaksi' => now(),
                //                 'jumlah_donasi' => $data['jumlah_donasi'],
                //                 'uraian'        => $data['uraian'],
                //             ]);
                //             $record->update([
                //                 'status' => 'diterima',
                //             ]);
                //         }
                //     ),
                Tables\Actions\Action::make('lokasi')
                    ->iconButton()
                    ->hidden(
                        fn($livewire)
                        => $livewire->activeTab === 'Distribusi')
                    ->tooltip('Lihat Lokasi')
                    ->icon('heroicon-o-map-pin')
                    ->color(
                        fn($record)
                        => $record->status->value !== 'distribusi'
                            ? 'info'
                            : 'gray'
                    )
                    ->disabled(
                        fn($record)
                        => $record->status->value === 'distribusi'
                    )
                    ->url(
                        fn($record)
                        => "https://www.google.com/maps/search/?api=1&query="
                            . $record->geo_lat
                            . ","
                            . $record->geo_long,
                        true
                    ),
                // There is no one can interrupt the process
                // because the process is done by the system
                // Tables\Actions\EditAction::make()
                //     ->iconButton(),
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
            'index'         => Pages\ListDistribusiKenclengs::route('/'),
            // 'create'        => Pages\CreateDistribusiKencleng::route('/create'),
            'edit'          => Pages\EditDistribusiKencleng::route('/{record}/edit'),
        ];
    }
}
