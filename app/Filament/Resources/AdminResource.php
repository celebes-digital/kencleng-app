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
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    // Just naming admin to internal for the sake of this example
    protected static ?int    $navigationSort    = 1;
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
                        $userLevel = Auth::user()->admin->level;

                        $option = [
                            'manajer'       => 'Manajer',
                            'admin'         => 'Admin Cabang',
                            'supervisor'    => 'Supervisor'
                        ];

                        if(in_array($userLevel, ['superadmin', 'principal'])) {
                            $option = array(
                                'direktur' => 'Direktur',
                                'admin_wilayah' => 'Admin Wilayah',
                            ) + $option;
                        }

                        if ($userLevel === 'superadmin') {
                            $option = array('principal' => 'Principal') + $option;
                        }

                        return $option;
                    })
                    ->live()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('telepon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('wilayah_id')
                    ->native(false)
                    ->hidden(
                        fn($get) => $get('level') !== 'admin_wilayah' && $get('level') !== 'direktur'
                    )
                    ->relationship('wilayah', 'nama_wilayah')
                    ->required(
                        function ($get) {
                            return $get('level') === 'admin_wilayah' || $get('level') === 'direktur';
                        }
                    ),
                Forms\Components\Select::make('cabang_id')
                    ->native(false)
                    ->hidden(
                        fn ($get) => $get('level') !== 'manajer' && $get('level') !== 'admin')
                    ->relationship('cabang', 'nama_cabang')
                    ->required(
                        function ($get) {
                            return $get('level') === 'manajer' || $get('level') === 'admin';
                        }
                    ),
                Forms\Components\Select::make('area_id')
                    ->native(false)
                    ->hidden(
                        fn ($get) => $get('level') !== 'supervisor')
                    ->relationship('area', 'nama_area')
                    ->required(
                        function ($get) {
                            return $get('level') === 'supervisor';
                        }
                    ),
                Forms\Components\Group::make([
                    Forms\Components\Hidden::make('is_admin')
                        ->default(true),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique('users', 'email', ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create'),
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
                $userLevel = Auth::user()->admin?->level ?? null;

                if( $userLevel !== 'superadmin' ) {
                    $query->where('level', '!=', 'superadmin')->where('level', '!=', 'principal');
                }

                if( $userLevel !== 'superadmin' && $userLevel !== 'principal' ) {
                    $query->where('level', '!=', 'direktur')->where('level', '!=', 'admin_wilayah');
                }

                if( $userLevel !== 'superadmin' && $userLevel !== 'principal' && $userLevel !== 'direktur' && $userLevel !== 'admin_wilayah' ) {
                    $query->where('level', '!=', 'manajer');
                }
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
