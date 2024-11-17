<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Penjadwalan extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 1;
    protected static ?string $navigationLabel   = 'Penjadwalan';
    protected static ?string $modelLabel        = 'Penjadwalan';
    protected static ?string $title             = 'Penjadwalan';
    protected static ?string $slug              = 'koleksi/penjadwalan';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(DistribusiKencleng::where('status', StatusDistribusi::DIISI))
            ->columns([
                TextColumn::make('kencleng.no_kencleng'),
                TextColumn::make('donatur.nama'),
            ])
            ->filters([])
            ->actions([]);
    }

    protected static string $view = 'filament.pages.koleksi.penjadwalan';
}
