<?php

namespace App\Livewire\Pages\Daftar;

use Livewire\Component;

use App\Filament\Components\ScannerQrCode;

use App\Models\District;
use App\Models\Kencleng;
use App\Models\Profile;
use App\Models\Province;
use App\Models\Subdistrict;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Notifications\Notification;

class Donatur extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public bool $showForm;

    public ?array $dataKencleng = [];
    public ?array $dataDonatur  = [];

    public function mount()
    {
        $this->getDataKenclengForm->fill($this->dataKencleng);
        $this->getDataDonaturForm->fill($this->dataDonatur);

        $this->showForm = false;
    }

    public function getDataKenclengForm(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    ScannerQrCode::make('scanner')
                        ->label('')
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            $kencleng = Kencleng::where('no_kencleng', $state)->first();

                            Notification::make()
                                ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                                ->success()
                                ->send();

                            $set('kencleng_id', $kencleng->id);
                        }),
                    Fieldset::make('')
                        ->schema([
                            Forms\Components\Select::make('kencleng_id')
                                ->label('ID Kencleng')
                                ->required()
                                ->options(
                                    Kencleng::all()->pluck('no_kencleng', 'id')
                                )
                                ->helperText('Hanya terisi jika scan QR Code Kencleng')
                                ->disabled()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Donatur')
                                ->helperText('Sesuikan dengan nama di KTP')
                                ->required(),
                            Forms\Components\TextInput::make('no_wa')
                                ->label('No. WhatsApp')
                                ->mask('9999 9999 9999')
                                ->helperText('Contoh: 0812 1234 1234')
                                ->required(),
                            Actions::make([
                                $this->nextAction()
                            ])
                            ->fullWidth()
                        ])->columns(1),
                ])->from('md'),
            ])
            ->statePath('dataKencleng');
    }

    public function nextAction(): Action
    {
        return Action::make('next')
            ->label('Lanjutkan')
            ->icon('heroicon-m-check')
            ->action(
                function () 
                {
                    // if(!$this->dataKencleng['kencleng_id'])
                    // {
                    //     Notification::make()
                    //         ->title('ID Kencleng tidak boleh kosong')
                    //         ->danger()
                    //         ->send();
                    //     return false;
                    // }

                    $donatur = 
                        Profile::where('nama', $this->dataKencleng['nama'])
                        ->where('no_wa', $this->dataKencleng['no_wa'])
                        ->first();

                    if(!$donatur || $donatur->no_wa != $this->dataKencleng['no_wa']) {
                        Notification::make()
                            ->title('Data donatur tidak ditemukan')
                            ->danger()
                            ->send();
                    }

                    $this->showForm = true;
                }
            );
    }

    public function getDataDonaturForm(Form $form): Form
    {
        return $form
            ->schema([
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
                        Forms\Components\Select::make('provinsi')
                            ->options(Province::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('kabupaten')
                            ->options(fn(Get $get) => District::query()
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
                            ->options(fn(Get $get) => Subdistrict::query()
                            ->where('district_id', $get('kabupaten'))
                            ->pluck('name', 'id'))
                            ->searchable()
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
                Fieldset::make('Dokumen')
                ->schema([
                    Forms\Components\FileUpload::make('foto')
                        ->image()
                        ->disk('public')
                        ->directory('foto')
                        ->visibility('private')
                        ->required(),
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
            ])
            ->statePath('dataDonatur');
    }

    protected function getForms(): array
    {
        return [
            'getDataKenclengForm',
            'getDataDonaturForm',
        ];
    }

    // Digunakan oleh scanner
    public function checkNoKencleng($noKencleng): bool
    {
        $kencleng = Kencleng::where('no_kencleng', $noKencleng)->first();

        if (!$kencleng) {
            Notification::make()
                ->title('Kencleng ' . $noKencleng  . ' tidak terdaftar')
                ->danger()
                ->send();

            return false;
        }

        return true;
    }

    public function render()
    {
        return view('livewire.pages.daftar.donatur');
    }
}
