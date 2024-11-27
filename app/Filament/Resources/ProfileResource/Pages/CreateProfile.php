<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\Area;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Collection;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

use Filament\Actions;
use Filament\Actions\Action;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

class CreateProfile extends CreateRecord
{
    protected static string $resource = ProfileResource::class;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Data Pengguna')
                        ->icon('heroicon-m-envelope')
                        ->schema([
                            Forms\Components\Fieldset::make('Data Pribadi')
                                ->schema([
                                    Forms\Components\Select::make('area_id')
                                        ->label('Area')
                                        ->native(false)
                                        ->options(
                                            Area::where('cabang_id', Auth::user()->admin->cabang_id)
                                            ->pluck('nama_area', 'id')->toArray())
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('group')
                                        ->placeholder('Pilih group')
                                        ->options([
                                            'distributor' => 'Distributor',
                                            'kolektor' => 'Kolektor',
                                            'donatur' => 'Donatur',
                                        ])
                                        ->native(false)
                                        ->live()
                                        ->required(),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required(fn(Get $get) => $get('group') !== 'donatur')
                                        ->unique('users')
                                        ->helperText(fn(Get $get) => $get('group') === 'donatur' ? 'Donatur tidak wajib untuk mengisi email' : 'Email harus unik')
                                        ->maxLength(255),
                                ]),
                        ])
                        ->columns(1),
                    Forms\Components\Wizard\Step::make('Detail Pengguna')
                        ->icon('heroicon-m-user-circle')
                        ->schema([
                            Forms\Components\Fieldset::make('Data Pribadi')
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

                            Forms\Components\Fieldset::make('Kontak')
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

                            Forms\Components\Fieldset::make('Alamat')
                                ->schema([
                                    Forms\Components\Select::make('provinsi')
                                        ->options(Province::all()->pluck('name', 'id')->toArray())
                                        ->searchable()
                                        ->live()
                                        ->required(),

                                    Forms\Components\Select::make('kabupaten')
                                        ->label('Kabupaten/Kota')
                                        ->options(fn (Get $get): Collection => District::query()
                                            ->where('province_id', $get('provinsi'))
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->live()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required(),
                                        ])
                                        ->createOptionUsing(function (array $data, Get $get): int {
                                            $data['province_id'] = $get('provinsi');
                                            return District::create($data)->getKey();
                                        })
                                        ->required(),

                                    Forms\Components\Select::make('kecamatan')
                                        ->options(fn (Get $get): Collection => Subdistrict::query()
                                            ->where('district_id', $get('kabupaten'))
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->live()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required(),
                                        ])
                                        ->createOptionUsing(function (array $data, Get $get): int {
                                            $data['district_id'] = $get('kabupaten');
                                            return Subdistrict::create($data)->getKey();
                                        })
                                        ->required(),

                                    Forms\Components\TextInput::make('kelurahan')
                                        ->required()
                                        ->maxLength(100),

                                    Forms\Components\TextInput::make('alamat')
                                        ->label('Detail alamat')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->columns(2),
                            Forms\Components\Fieldset::make('Dokumen')
                                ->schema([
                                    Forms\Components\FileUpload::make('foto')
                                        ->label('Foto Profil')
                                        ->image()
                                        ->disk('public')
                                        ->directory('foto')
                                        ->visibility('private')
                                        ->required(fn(Get $get) => $get('group') !== 'donatur'),
                                    Forms\Components\FileUpload::make('foto_ktp')
                                        ->label('Foto KTP')
                                        ->disk('public')
                                        ->directory('foto-ktp')
                                        ->visibility('private')
                                        ->imageEditor()
                                        ->image()
                                        ->required(fn(Get $get) => $get('group') !== 'donatur'),
                                ])
                                ->columns(2),
                        ]),
                ])
                    ->submitAction(
                        Action::make('Tambahkan')
                            ->submit('create'),
                    )
                    ->columns(1),
            ])
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->button()
                ->color('gray')
                ->url(ProfileResource::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($data['group'] === 'donatur' && $data['email'] === null) {
            $data['email'] = $data['nik'] . '@donatur.com';
        }

        $user = User::create([
            'email'      => $data['email'],
            'password'   => bcrypt(env('DEFAULT_PASSWORD', 'kencleng123')),
        ]);

        $data['provinsi']   = Province::find($data['provinsi'])->name;
        $data['kabupaten']  = District::find($data['kabupaten'])->name;
        $data['kecamatan']  = Subdistrict::find($data['kecamatan'])->name;

        $data['user_id'] = $user->id;
        $profile = static::getModel()::create($data);

        return $profile;
    }

    // Dilakukan terpisah oleh admin
    // protected function afterCreate()
    // {
    //     $user = User::find($this->record->user_id);
    //     $this->sendPasswordResetLink($user->email);
    // }

    private function sendPasswordResetLink($email): void
    {
        $status = Password::sendResetLink(
            ['email' => $email]
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        Notification::make()
            ->title('Verifikasi Email Terkirim')
            ->body('Email verifikasi telah dikirim ke alamat email pengguna.')
            ->icon('heroicon-o-envelope')
            ->iconColor('success')
            ->send();
        return;
    }
}
