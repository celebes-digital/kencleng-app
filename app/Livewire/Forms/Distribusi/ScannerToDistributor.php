<?php

namespace App\Livewire\Forms\Distribusi;

use Livewire\Component;
use App\Enums\StatusKencleng;

use App\Models;

use Filament\Forms;
use Filament\Tables;

use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Exceptions\Halt;

class ScannerToDistributor 
    extends Component 
    implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    public int $jumlahDistribusi;

    public ?array $data = [];

    public function mount(): void
    {
        $this->jumlahDistribusi = 0;

        $this->form->fill($this->data);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('distributor_id')
                    ->label('Distributor')
                    ->placeholder('Pilih Distributor')
                    ->searchable()
                    ->searchPrompt('Masukkan minimal 3 karakter')
                    ->noSearchResultsMessage(fn (): string => "Donatur tidak ditemukan")
                    ->getSearchResultsUsing(
                        function (string $search): array {
                            if((strlen($search) < 3)) return [];

                            return Models\Profile::where('nama', 'like', "%{$search}%")
                                ->limit(7)
                                ->pluck('nama', 'id')
                                ->toArray();
                        }
                    )
                    ->getOptionLabelUsing(fn($value): ?string => Models\Profile::find($value)?->nama)
                    ->columnSpan(2)
                    ->required(),

                Forms\Components\Select::make('kencleng_id')
                    ->label('ID Kencleng')
                    ->placeholder('Scan QR Code Kencleng')
                    ->searchable()
                    ->searchPrompt('Scanning QR Code kencleng')
                    ->noSearchResultsMessage('Kencleng tidak tersedia')
                    ->getSearchResultsUsing(
                        function (string $search): array {
                            if ((strlen($search) < 3)) return [];

                            return Models\Kencleng::where('no_kencleng', 'like', "%{$search}%")
                                ->where('status', StatusKencleng::TERSEDIA)
                                ->limit(7)
                                ->pluck('no_kencleng', 'id')
                                ->toArray();
                        }
                    )
                    ->getOptionLabelUsing(fn($value): ?string => Models\Kencleng::find($value)?->no_kencleng)
                    ->columnSpan(2)
                    ->required(),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('saveAction')
                        ->label('Distribusi')
                        ->button()
                        ->icon('heroicon-o-chevron-right')
                        ->iconPosition(IconPosition::After)
                        ->action(fn () => $this->saveAction())
                ])
                ->verticalAlignment(VerticalAlignment::End)
                ->fullWidth()
            ])
            ->statePath('data')
            ->columns([
                'sm' => 5,
                'md' => 5,
                'lg' => 5,
                'xl' => 5,
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Models\DistribusiKencleng::whereNotNull('distributor_id'))
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('No. Kencleng'),
                Tables\Columns\TextColumn::make('distributor.nama')
                    ->label('Distributor'),
                Tables\Columns\TextColumn::make('tgl_distribusi')
                    ->label('Tanggal Distribusi')
            ])
            ->filters([])
            ->defaultSort('tgl_distribusi', 'desc');
    }

    public function saveAction()
    {
        try {
            // Sekaligus menjalankan validasi form
            $data = $this->form->getState();

            // Inisialisasi data distribusi kencleng
            // DIstributor ID dan status jadi distribusi
            $query = Models\DistribusiKencleng::create([
                'kencleng_id'       => $data['kencleng_id'],
                'distributor_id'    => $data['distributor_id'],
                'tgl_distribusi'    => now(),
                'status'            => 'distribusi',
            ]);

            if (!$query) throw new Halt('Gagal menyimpan data');

            $this->form->fill(['distributor_id' => $data['distributor_id']]);

            Notification::make()
                ->title('Berhasil melakukan distribusi kencleng ke distributor')
                ->success()
                ->send();
            
            $this->jumlahDistribusi++;
        }
        catch (Halt $e)
        {
            Notification::make()
                ->title($e->getMessage() ?? 'Gagal menyimpan data')
                ->danger()
                ->send();
            return;
        }
    }

    public function render()
    {
        return view('livewire.forms.distribusi.scanner-to-distributor', [
            'jumlahDistribusi' => $this->jumlahDistribusi,
        ]);
    }
}
