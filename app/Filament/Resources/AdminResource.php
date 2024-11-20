<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    // Just naming admin to internal for the sake of this example
    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationGroup   = 'Setting';
    protected static ?string $label             = 'internal';
    protected static ?string $navigationIcon    = 'heroicon-o-inbox-stack';
    protected static ?string $slug              = 'internal';
    protected static ?string $breadcumb         = 'internal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('level')
                    ->native(false)
                    ->options(
                    function() {
                        $option = [
                            'admin' => 'Admin',
                            'manajer' => 'Manager',
                        ];

                        if (Auth::user()->admin->level === 'superadmin') {
                            $option['principal'] = 'Principal';
                        }

                        return $option;
                    })
                    ->disableOptionWhen(fn ($value) => $value === 'principal' && Auth::user()->admin->level !== 'superadmin')
                    ->required(),
                Forms\Components\TextInput::make('telepon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('cabang_id')
                    ->native(false)
                    ->relationship('cabang', 'nama_cabang')
                    ->required(),
                Forms\Components\Group::make([
                    Forms\Components\Hidden::make('is_admin')
                        ->default(true),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique('users', 'email')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->inline(false),
                ])
                ->relationship('user')
                ->columnSpanFull()
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('level', 'admin')->orWhere('level', 'manajer');
            })
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon'),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->searchable()
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
