<?php

namespace App\Filament\Resources;

use App\Filament\Components\Scanner;
use App\Filament\Components\ScannerQrCode;
use App\Filament\Resources\DistribusiKenclengResource\Pages;
use App\Livewire\ScannerQR;
use App\Models\DistribusiKencleng;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\DistribusiKenclengResource\Pages\CreateDistribusiKencleng;
use GuzzleHttp\Promise\Create;

class DistribusiKenclengResource extends Resource
{
    protected static ?string $model = DistribusiKencleng::class;

    protected static ?string $modelLabel        = 'Distribusi Kencleng';
    protected static ?string $label             = 'Data Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'distribusi-kencleng';
    protected static ?string $navigationGroup   = 'Kencleng';
    protected static ?int    $navigationSort    = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    ScannerQrCode::make('scanner')
                        ->afterStateUpdated(fn (Set $set, $state) => $set('kencleng_id', $state)),
                    TextInput::make('kencleng_id')
                        ->label('No. Kencleng')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor.nama')
                    ->label('Distributor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_lat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_long')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDistribusiKenclengs::route('/'),
            'create' => Pages\CreateDistribusiKencleng::route('/create'),
            'edit' => Pages\EditDistribusiKencleng::route('/{record}/edit'),
        ];
    }
}
