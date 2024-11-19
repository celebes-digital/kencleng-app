<?php

namespace App\Filament\Pages\Koleksi;

use App\Models\DistribusiKencleng;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class JadwalKoleksi 
    extends Page 
    implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.pages.koleksi.jadwal-koleksi';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return !$user->is_admin;
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $modelLabel        = 'Jadwal Koleksi';
    protected static ?string $label             = 'Jadwal Koleksi';
    protected static ?string $slug              = 'koleksi/jadwal';

    public function table(Table $table): Table
    {
        return $table
            ->query(DistribusiKencleng::query())
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng'),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur'),
                Tables\Columns\TextColumn::make('donatur.no_wa')
                    ->label('WA Donatur'),
            ])
            ->filters([])
            ->actions([
                Action::make('koleksi')
                ->label('Koleksi')
                ->button()
                ->icon('heroicon-o-shopping-cart')
                ->iconPosition(IconPosition::After)
                ->modalSubmitActionLabel('Konfirmasi')
                ->modalDescription('Koleksi kencleng dari donatur')
                ->modal()
                ->form(
                    fn ($record) => [
                        Forms\Components\TextInput::make('donatur_id')
                        ->readOnly()
                        ->default($record->donatur->nama)
                        ->label('Donatur'),
                        Forms\Components\TextInput::make('alamat')
                        ->readOnly()
                        ->default($record->donatur->alamat)
                        ->label('Alamat'),
                        Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->prefix('IDR')
                        ->required(),
                        Forms\Components\Radio::make('status')
                        ->label('Status')
                        ->default('lanjut_tetap')
                        ->options([
                            'lanjut_tetap'      => 'Lanjut Tetap',
                            'lanjut_pindah'     => 'Lanjut Pindah',
                            'berhenti'          => 'Berhenti',
                        ])
                        ->inline()
                        ->inlineLabel(false)
                        ->required(),
                    ]
                )
                ->action(
                    function ($record, $values) {
                        dd($record, $values);
                    }
                ),

                Tables\Actions\Action::make('lokasi')
                ->iconButton()
                ->tooltip('Lihat Lokasi')
                ->icon('heroicon-o-map-pin')
                ->color(
                    fn($record)
                    => $record->geo_lat !== null
                        ? 'info'
                        : 'gray'
                )
                ->disabled(
                    fn($record)
                    => $record->geo_lat === null
                )
                ->url(
                    fn($record)
                    => "https://www.google.com/maps/search/?api=1&query="
                    . $record->geo_lat
                        . ","
                        . $record->geo_long,
                    true
                ),
            ]);
    }
}
