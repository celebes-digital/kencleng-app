<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KenclengResource\Pages;
use App\Filament\Resources\KenclengResource\RelationManagers;
use App\Models\Kencleng;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KenclengResource extends Resource
{
    protected static ?string $model             = Kencleng::class;
    protected static ?string $label             = 'Data Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-arrow-down';
    protected static ?string $slug              = 'kencleng';
    protected static ?string $navigationGroup   = 'Kencleng';
    protected static ?int    $navigationSort    = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('batch_kencleng_id')
                    ->label('Batch ke')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('no_kencleng')
                    ->required()
                    ->maxLength(10),
                Forms\Components\FileUpload::make('qr_image')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_kencleng_id')
                    ->label('Batch ke')
                    ->numeric()
                    ->grow(false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_kencleng')
                    ->grow(false)
                    ->extraAttributes(['class' => 'font-bold text-lg text-blue-500'])
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qr_image')
                    ->size('150px')
                    ->alignCenter()
                    ->square(),
            ])
            ->defaultSortOptionLabel('batch_kencleng_id.nama_batch', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListKenclengs::route('/'),
            'create' => Pages\CreateKencleng::route('/create'),
            'edit' => Pages\EditKencleng::route('/{record}/edit'),
        ];
    }
}

