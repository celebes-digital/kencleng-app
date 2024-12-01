<?php

namespace App\Filament\Pages\Distribusi\Distributor;

use App\Libraries\WhatsappAPI;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use App\Models\User;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterDonaturByDistributor extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public static function canAccess(): bool
    {
        return !Auth::user()->is_admin && Auth::user()->profile->group === 'distributor';
    }
    
    public ?string $heading         = 'Tambah Donatur';
    protected static string $view   = 'filament.pages.distribusi.distributor.register-donatur-by-distributor';
    
    protected static ?string $slug                  = 'distribusi/register-donatur';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->rules(['email', 'unique:users,email'])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->autofocus()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->unique()
                            ->mask('9999 9999 9999 9999')
                            ->rules(['nullable', 'size:16'])
                            ->stripCharacters(' '),
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
                            ->options(fn(Forms\Get $get): Collection => District::query()
                                ->where('province_id', $get('provinsi'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data, Forms\Get $get): int {
                                $data['province_id'] = $get('provinsi');
                                return District::create($data)->getKey();
                            })
                            ->required(),

                        Forms\Components\Select::make('kecamatan')
                            ->options(fn(Forms\Get $get): Collection => Subdistrict::query()
                                ->where('district_id', $get('kabupaten'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data, Forms\Get $get): int {
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
                            ->visibility('private'),
                        Forms\Components\FileUpload::make('foto_ktp')
                            ->label('Foto KTP')
                            ->disk('public')
                            ->directory('foto-ktp')
                            ->visibility('private')
                            ->imageEditor()
                            ->image(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url(ListDonatur::getUrl()),
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }

    public function save()
    {
        try {
            $data = $this->form->getState();

            $userLogin = Auth::user();

            if (!$userLogin->is_admin && $userLogin->profile->group !== 'distributor') {
                throw new Halt('Anda tidak memiliki akses untuk menambahkan donatur');
            }

            $data['group']          = 'donatur';
            $data['area']           = $userLogin->profile->area_id;
            $data['cabang']         = $userLogin->profile->cabang_id;
            $data['wilayah']        = $userLogin->profile->wilayah_id;
            $data['distributor_id'] = $userLogin->profile->id;

            if ($data['group'] === 'donatur' && $data['email'] === null) {
                do {
                    $data['email'] = Str::random(10) . env('DEFAULT_EMAIL_DOMAIN', '@kencleng.id');
                } while (User::where('email', $data['email'])->exists());
            }

            $user = User::create([
                'email'      => $data['email'],
                'password'   => bcrypt(env('DEFAULT_PASSWORD', 'kencleng123')),
            ]);

            $data['provinsi']   = Province::find($data['provinsi'])->name;
            $data['kabupaten']  = District::find($data['kabupaten'])->name;
            $data['kecamatan']  = Subdistrict::find($data['kecamatan'])->name;

            $user->profile()->create($data);

            Notification::make()
            ->success()
            ->title('Donatur berhasil ditambahkan')
            ->send();

            $whatsapp = new WhatsappAPI($data['no_wa']);

            $data = [
                'nama'      => $data['nama'],
                'email'     => $data['email'],
                'group'     => 'Donatur',
                'kelamin'   => $data['kelamin'],
            ];

            $whatsapp->getTemplateMessage('SetelahRegistrasi', $data);
            $whatsapp->send();

            redirect()->to(ListDonatur::getUrl());
            $this->form->fill([]);
        } catch (Halt $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage() ?? 'Donatur gagal ditambahkan')
                ->send();
            return;
        }
    }
}
