<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Setting';
    protected static ?string $label             = 'area';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-stack';
    protected static ?string $slug              = 'area';
    protected static ?string $breadcumb         = 'area';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cabang_id')
                    ->disabled(fn () => Auth::user()->admin?->level === 'manajer')
                    ->default(function () {
                        $user = Auth::user();
                        return $user->admin?->level === 'manajer' ? $user->admin?->cabang_id : null;
                    })
                    ->dehydrated()
                    ->native(false)
                    ->relationship('cabang', 'nama_cabang')
                    ->required(),
                Forms\Components\TextInput::make('nama_area')
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAreas::route('/'),
        ];
    }
}
