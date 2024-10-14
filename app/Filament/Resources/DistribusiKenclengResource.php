<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistribusiKenclengResource\Pages;
use App\Filament\Resources\DistribusiKenclengResource\RelationManagers;
use App\Models\DistribusiKencleng;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistribusiKenclengResource extends Resource
{
    protected static ?string $model = DistribusiKencleng::class;

    protected static ?string $modelLabel = 'Distribusi Kencleng';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kencleng_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('donator_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kolektor_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('distributor_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('geo_lat')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('geo_long')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tgl_distribusi')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_pengambilan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kencleng_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('donator_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor_id')
                    ->numeric()
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
