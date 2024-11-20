<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\Area;
use App\Models\DistribusiKencleng;
use App\Models\Profile;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Forms\Components\Select;

use Illuminate\Support\Facades\Auth;

class Penjadwalan extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }

    protected static string $view = 'filament.pages.koleksi.penjadwalan';

    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 1;
    protected static ?string $navigationLabel   = 'Penjadwalan';
    protected static ?string $modelLabel        = 'Penjadwalan';
    protected static ?string $title             = 'Penjadwalan';
    protected static ?string $slug              = 'koleksi/penjadwalan';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                DistribusiKencleng::where('status', StatusDistribusi::DIISI)
                ->where('tgl_batas_pengambilan', '<=', now()->addDays(7)))
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.no_wa')
                    ->label('No. Whatsapp'),
                Tables\Columns\TextColumn::make('area.nama_area')
                    ->label('Area')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_batas_pengambilan')
                    ->label('Jadwal')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('pilihKolektor')
                ->button()
                ->icon('heroicon-o-forward')
                ->color('primary')
                ->modalSubmitActionLabel('Jadwalkan')
                ->form(
                    fn() => [
                        Select::make('kolektor_id')
                        ->label('Kolektor')
                        ->native(false)
                        ->options(Profile::where('group', 'kolektor')->pluck('nama', 'id')->toArray())
                    ]
                )
                ->action(
                    function (DistribusiKencleng $record, $data) 
                    {
                        $record->update([
                            'kolektor_id' => $data['kolektor_id'],
                        ]);
                    }
                ),
                Tables\Actions\Action::make('aturArea')
                ->button()
                ->icon('heroicon-o-map')
                ->color('primary')
                ->modalSubmitActionLabel('Atur Area')
                ->form(
                    fn() => [
                        Select::make('area_id')
                        ->label('Area')
                        ->native(false)
                        ->relationship('area')
                        ->options(Area::all()->pluck('nama_area', 'id')->toArray())
                    ]
                )
                ->action(
                    function (DistribusiKencleng $record, $data) 
                    {
                        $record->update([
                            'area_id' => $data['area'],
                        ]);
                    }
                ),
            ]);
    }
}
