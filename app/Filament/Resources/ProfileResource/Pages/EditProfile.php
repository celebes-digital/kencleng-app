<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProfileResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['email'] = User::find($data['user_id'])->email;
        return $data;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Data Pribadi')
                    ->schema([
                        Forms\Components\Select::make('group')
                            ->placeholder('Pilih group')
                            ->options([
                                'disrtibutor' => 'Distributor',
                                'kolektor' => 'Kolektor',
                                'donatur' => 'Donatur',
                            ])
                            ->native(false)
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(fn(Get $get) => $get('group') !== 'donatur')
                            ->unique('users')
                            ->helperText(fn(Get $get) => $get('group') === 'donatur' ? 'Donatur tidak wajib untuk mengisi email' : 'Email harus unik')
                            ->maxLength(255),
                    ]),
                Fieldset::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->autofocus()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->unique()
                            ->required(fn(Get $get) => $get('group') !== 'donatur')
                            ->mask('9999 9999 9999 9999')
                            ->stripCharacters(' ')
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
                            ->native(false)
                            ->required(),
                        Forms\Components\TextInput::make('pekerjaan')
                            ->required()
                            ->maxLength(100)
                    ])
                    ->columns(3),
                Fieldset::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->required()
                            ->tel()
                            ->mask('9999 9999 9999 99')
                            ->stripCharacters(' ')
                            ->minLength(5)
                            ->maxLength(15),
                        Forms\Components\TextInput::make('no_wa')
                            ->label('Nomor WhatsApp Aktif')
                            ->required()
                            ->tel()
                            ->mask('9999 9999 9999 99')
                            ->stripCharacters(' ')
                            ->minLength(5)
                            ->maxLength(15),
                    ])
                    ->columns(2),
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
                        Forms\Components\FileUpload::make('foto')
                            ->image()
                            ->required(),
                        Forms\Components\FileUpload::make('foto_ktp')
                            ->label('Foto KTP')
                            ->image()
                            ->required(fn(Get $get) => $get('group') !== 'donatur'),
                    ])
            ])->columns(1);
    }
}
