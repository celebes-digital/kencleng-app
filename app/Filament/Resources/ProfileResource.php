<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Wizard;
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
                        Fieldset::make('Data Pribadi')
                        ->schema([
                        Forms\Components\Select::make('group')
                            ->placeholder('Pilih group')
                            ->options([
                                'admin' => 'Admin',
                                'donatur' => 'Donatur',
                                'kolektor' => 'Kolektor',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->length(16),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection()
                            ->placeholder('Pilih tanggal lahir')
                            ->required(),
                        Forms\Components\Select::make('kelamin')
                            ->label('Jenis Kelamin')
                            ->placeholder('Pilih jenis kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('pekerjaan')
                            ->required()
                            ->maxLength(100)
            ])
                    ->columns(3),
                        Fieldset::make('Kontak')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('no_hp')
                                    ->label('Nomor HP')
                                    ->required()
                                    ->tel()
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('no_wa')
                                    ->required()
                                    ->maxLength(15),
                                    ])
                                    ->columns(3),
                        Fieldset::make('Alamat')
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
                            ->columns(3),
                        Fieldset::make('Dokumen')
                            ->schema([
                                Forms\Components\FileUpload::make('poto')
                                    ->image()
                                    ->required(),
                                Forms\Components\FileUpload::make('poto_ktp')
                                    ->image()
                                    ->required(),
                            ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
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
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'gray',
                        'donatur' => 'success',
                        'kolektor' => 'warning',
                        'distributor' => 'danger',
                    })
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
