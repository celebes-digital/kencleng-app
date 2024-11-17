<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use App\Models\Infaq;
use App\Models\Kencleng;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class Konfirmasi 
    extends Page 
    implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationLabel   = 'Konfirmasi';
    protected static ?string $modelLabel        = 'Konfirmasi';
    protected static ?string $title             = 'Konfirmasi';
    protected static ?string $slug              = 'koleksi/konfirmasi';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kencleng_id')
                    ->label('ID Kencleng')
                    ->placeholder('Scan QR Code kencleng')
                    ->searchable()
                    ->searchPrompt('Scan kencleng...')
                    ->noSearchResultsMessage('Kencleng tidak ditemukan')
                    ->live()
                    ->getSearchResultsUsing(
                        function (string $search): array 
                        { 
                            if(strlen($search) < 3) return [];

                            return DistribusiKencleng::where('kenclengs.no_kencleng', 'like', "%{$search}%")
                            ->where('distribusi_kenclengs.status', StatusDistribusi::KEMBALI)
                            ->orWhere('distribusi_kenclengs.status', StatusDistribusi::DIISI)
                            ->join('kenclengs', 'distribusi_kenclengs.kencleng_id', '=', 'kenclengs.id')
                            ->limit(5)
                            ->pluck('kenclengs.no_kencleng', 'kencleng_id')
                            ->toArray();
                        })
                    ->getOptionLabelUsing(
                        function ($value): ?string
                        {
                            if($value === null) return null;

                            return Kencleng::find($value)?->no_kencleng;
                        })
                    ->columnSpan([
                        'lg' => 2
                    ]),

                Actions::make([
                    Action::make('submit')
                        ->label('Submit')
                        ->button()
                        ->form(
                            function ($state) {
                                $distribusiKencleng = 
                                    DistribusiKencleng::where('kencleng_id', $state['kencleng_id'])
                                    ->where('status', StatusDistribusi::KEMBALI)
                                    ->orWhere('status', StatusDistribusi::DIISI)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                                // if(!$distribusiKencleng) {
                                //     Notification::make()
                                //         ->title('Distribusi kencleng tidak ditemukan')
                                //         ->danger()
                                //         ->send();

                                //     return null;
                                // }

                                return [
                                    TextInput::make('donatur.nama')
                                        ->label('Donatur')
                                        ->default($distribusiKencleng->donatur->nama)
                                        ->disabled(),
                                    TextInput::make('donasi')
                                        ->label('Jumlah')
                                        ->default(
                                            $distribusiKencleng->jumlah 
                                            ? Number::currency($distribusiKencleng->jumlah, 'IDR', 'id') 
                                            : 0)
                                        ->prefixIcon('heroicon-o-banknotes')
                                        ->visible(
                                            $distribusiKencleng->status === StatusDistribusi::KEMBALI),
                                    TextInput::make('jumlah_donasi')
                                        ->label('Donasi Diterima')
                                        ->prefix('Rp')
                                        ->numeric()
                                        ->minValue(0)
                                        ->required(),
                                    Textarea::make('uraian')
                                        ->label('Keterangan Tambahan')
                                        ->autosize()
                                        ->rows(3)
                                ];
                            }
                        )
                        ->action(
                            function (?DistribusiKencleng $record, $data) {
                                if(!$record) return;

                                Infaq::create([
                                    'distribusi_id' => $record->id,
                                    'tgl_transaksi' => now(),
                                    'jumlah_donasi' => $data['jumlah_donasi'],
                                    'uraian'        => $data['uraian'],
                                ]);

                                $record->update([
                                    'status' => 'diterima',
                                ]);
                            }
                        ),
                ])
                ->fullWidth()
                ->verticallyAlignEnd()
            ])
            ->columns([
                'md' => 2,
                'lg' => 3,
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DistribusiKencleng::where('status', StatusDistribusi::KEMBALI))
            ->columns([
                TextColumn::make('kencleng.no_kencleng'),
                TextColumn::make('kolektor.nama'),
            ])
            ->filters([])
            ->actions([]);
    }

    protected static string $view = 'filament.pages.koleksi.konfirmasi';
}
