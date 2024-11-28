<?php

namespace App\Filament\Pages\Distribusi\Distributor;

use App\Models\Profile;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListDonatur extends Page implements HasTable
{
    use InteractsWithTable;

    public static function canAccess(): bool
    {
        return !Auth::user()->is_admin && Auth::user()->profile->group === 'distributor';
    }

    public ?string $heading                     = 'Daftar Donatur';
    protected static string $view               = 'filament.pages.distribusi.distributor.list-donatur';
    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationIcon    = 'heroicon-o-users';
    protected static ?string $navigationLabel   = 'Donatur';
    protected static ?string $modelLabel        = 'Donatur';
    protected static ?string $label             = 'Donatur';
    protected static ?string $slug              = 'distribusi/list-donatur';

    public function table(Table $table): Table
    {
        return $table
            ->query(Profile::where('distributor_id', Auth::user()->profile->id))
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('No WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelamin')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provinsi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->actions([
                //
            ])
            ->filters([
                //
            ])->headerActions([
                // Action::make('create')
                //     ->label('Tambah Donatur')
                //     ->icon('heroicon-o-plus')
                //     ->url(RegisterDonaturByDistributor::getUrl()),
            ]);;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Donatur')
                ->icon('heroicon-o-plus')
                ->url(RegisterDonaturByDistributor::getUrl()),
        ];
    }
}
