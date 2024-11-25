<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WilayahResource\Pages;
use App\Filament\Resources\WilayahResource\RelationManagers;
use App\Models\Wilayah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WilayahResource extends Resource
{
    protected static ?string $model = Wilayah::class;

    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationGroup   = 'Setting';
    protected static ?string $label             = 'wilayah';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-stack';
    protected static ?string $slug              = 'wilayah';
    protected static ?string $breadcumb         = 'wilayah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_wilayah')
                ->required()
                ->maxLength(255),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_wilayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListWilayahs::route('/'),
            // 'create' => Pages\CreateWilayah::route('/create'),
            // 'edit' => Pages\EditWilayah::route('/{record}/edit'),
        ];
    }
}
