<?php

namespace App\Filament\Resources;

use App\Models\Profile;
use Filament\Resources\Resource;
use App\Filament\Resources\ProfileResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?int $navigationSort       = 2;
    protected static ?string $slug              = 'pengguna';
    protected static ?string $modelLabel        = 'Pengguna';
    protected static ?string $navigationIcon    = 'heroicon-o-users';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Data Pribadi')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('nama')
                            ->label('Nama'),
                        TextEntry::make('nik')
                            ->label('NIK')
                            ->visible(fn($record) => $record->group !== 'donatur'),
                        TextEntry::make('pekerjaan')
                            ->label('Pekerjaan'),
                    ])
                    ->columns(2),
                Section::make('Kontak')
                    ->schema([
                        TextEntry::make('no_hp')
                            ->label('Nomor HP'),
                        TextEntry::make('no_wa')
                            ->label('Nomor WhatsApp Aktif'),
                    ])
                    ->columns(2),
                Section::make('Alamat')
                    ->schema([
                        TextEntry::make('alamat')
                            ->label('Alamat'),
                        TextEntry::make('kelurahan')
                            ->label('Kelurahan'),
                        TextEntry::make('kecamatan')
                            ->label('Kecamatan'),
                        TextEntry::make('kabupaten')
                            ->label('Kabupaten'),
                        TextEntry::make('provinsi')
                            ->label('Provinsi'),
                    ])
                    ->columns(3),
                Section::make('Dokumen')
                    ->schema([
                        // Uncomment the following lines if you want to display the images
                        ImageEntry::make('foto')
                            ->label('Foto'),
                        ImageEntry::make('foto_ktp')
                            ->label('Foto KTP')
                            // ->visible(fn($record) => $record->group !== 'donatur'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListProfiles::route('/'),
            'create'    => Pages\CreateProfile::route('/create'),
            'edit'      => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
