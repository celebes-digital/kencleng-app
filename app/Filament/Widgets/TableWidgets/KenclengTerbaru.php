<?php

namespace App\Filament\Widgets\TableWidgets;

use App\Models\DistribusiKencleng;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KenclengTerbaru extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    
    public function getTableHeading(): string
    {
        // dd($this->user->profile);
        if ( $this->user->profile->group === 'donatur' ) {
            return 'Donasi terbaru';
        }

        if ( $this->user->profile->group === 'distributor' ) {
            return 'Distribusi terbaru';
        }

        if ( $this->user->profile->group === 'kolektor' ) {
            return 'Koleksi terbaru';
        }

        return 'Data terbaru';
    }

    public function getDataQuery(Builder $query): Builder
    {
        if ($this->user->profile->group === 'donatur') {
            return $query->where('donatur_id', $this->user->profile->id);
        }

        if ($this->user->profile->group === 'distributor') {
            return $query->where('distributor_id', $this->user->profile->id);
        }

        if ($this->user->profile->group === 'kolektor') {
            return $query->where('kolektor_id', $this->user->profile->id);
        }

        return $query;
    }

    public static function canView(): bool
    {
        return !Auth::user()->is_admin;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = DistribusiKencleng::query();
                $query = $this->getDataQuery($query);

                return $query->latest()->limit(5);
            })
            ->columns([
                TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng'),
                TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->default('Belum ada'),
                TextColumn::make('tgl_distribusi')
                    ->label('Tanggal Distribusi')
                    ->hidden(fn () => $this->user->profile->group !== 'distributor'),
                TextColumn::make('tgl_aktivasi')
                    ->label('Tanggal Aktivasi')
                    ->hidden(fn () => $this->user->profile->group !== 'donatur'),
                TextColumn::make('tgl_batas_pengambilan')
                    ->label('Deadline'),
                TextColumn::make('tgl_pengambilan')
                    ->label('Tanggal Pengambilan')
                    ->hidden(fn () => $this->user->profile->group !== 'kolektor'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->placeholder('Belum ada'),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5);
    }
}
