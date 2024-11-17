<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Konfirmasi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationLabel   = 'Konfirmasi';
    protected static ?string $modelLabel        = 'Konfirmasi';
    protected static ?string $title             = 'Konfirmasi';
    protected static ?string $slug              = 'koleksi/konfirmasi';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->query(DistribusiKencleng::where('status', StatusDistribusi::KEMBALI))
            ->columns([
                TextColumn::make('kencleng.no_kencleng'),
                TextColumn::make('kolektor.nama'),
            ])
            ->filters([])
            ->actions([]);
    }

    protected static string $view = 'filament.pages.koleksi.konfirmasi';
}
