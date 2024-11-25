<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CabangResource\Pages;
use App\Filament\Resources\CabangResource\RelationManagers;
use App\Models\Cabang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CabangResource extends Resource
{
    protected static ?string $model = Cabang::class;

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Setting';
    protected static ?string $label             = 'cabang';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-stack';
    protected static ?string $slug              = 'cabang';
    protected static ?string $breadcumb         = 'cabang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('wilayah_id')
                    ->disabled(fn() => Auth::user()->admin?->level === 'manajer')
                    ->default(function () {
                        $user = Auth::user();
                        return $user->admin?->level === 'manajer' ? $user->admin?->cabang_id : null;
                    })
                    ->dehydrated()
                    ->native(false)
                    ->relationship('wilayah', 'nama_wilayah')
                    ->required(),
                Forms\Components\TextInput::make('nama_cabang')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_cabang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wilayah.nama_wilayah')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCabangs::route('/'),
            // 'create' => Pages\CreateCabang::route('/create'),
            // 'edit' => Pages\EditCabang::route('/{record}/edit'),
        ];
    }
}
