<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->required(),
                        Forms\Components\Select::make('kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->required()
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\TextInput::make('no_wa')
                            ->required()
                            ->maxLength(15),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\TextInput::make('alamat')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kelurahan')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('kecamatan')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('kabupaten')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('provinsi')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('pekerjaan')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('poto')
                            ->image()
                            ->required(),
                        Forms\Components\FileUpload::make('poto_ktp')
                            ->image()
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Grup')
                    ->schema([
                        Forms\Components\Select::make('group')
                            ->options([
                                'admin' => 'Admin',
                                'donatur' => 'Donatur',
                                'kolektor' => 'Kolektor',
                            ])
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelamin'),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('poto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('poto_ktp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->searchable(),
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
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
