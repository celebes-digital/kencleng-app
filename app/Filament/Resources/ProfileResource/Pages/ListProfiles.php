<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\Profile;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

use Filament\Tables;
use Filament\Tables\Table;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'           => Tab::make(),
            'Distributor'   => Tab::make()
                ->badgeColor('info')
                ->modifyQueryUsing(fn($query) => $query->where('group', 'distributor'))
                ->badge(fn() => Profile::query()->where('group', 'distributor')->count()),
            'Kolektor'      => Tab::make()
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->where('group', 'kolektor'))
                ->badge(fn() => Profile::query()->where('group', 'kolektor')->count()),
            'Donatur'      => Tab::make()
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('group', 'donatur'))
                ->badge(fn() => Profile::query()->where('group', 'donatur')->count()),
        ];
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelamin')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provinsi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('no_hp')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('No WA')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('poto')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('poto ktp')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin'         => 'gray',
                        'donatur'       => 'success',
                        'kolektor'      => 'warning',
                        'distributor'   => 'info',
                        default         => 'primary',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable(),
                Tables\Columns\IconColumn::make('user.is_active')
                    ->label('Status')
                    ->icon(fn(string $state): string => match ($state) {
                        '1' => 'heroicon-s-check-circle',
                        '0' => 'heroicon-s-x-circle',
                        default => 'heroicon-s-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
