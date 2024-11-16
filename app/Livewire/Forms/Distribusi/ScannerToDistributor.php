<?php

namespace App\Livewire\Forms\Distribusi;

use App\Enums\StatusKencleng;
use Livewire\Component;

use App\Models;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class ScannerToDistributor extends Component implements HasForms
{
    use InteractsWithForms;

    public array $newDistribusi;

    public ?array $data = [];

    public function mount(): void
    {
        $this->newDistribusi = [];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Select::make('distributor_id')
                    ->label('Distributor')
                    ->options(Models\Profile::where('group', 'distributor')->pluck('nama', 'id'))
                    ->live(true)
                    ->searchable()
                    ->required()
                    ->optionsLimit(10),

                Components\Select::make('kencleng_id')
                    ->label('ID Kencleng')
                    ->options(Models\Kencleng::all()->pluck('no_kencleng', 'id'))
                    ->searchable()
                    ->required()
                    ->optionsLimit(2),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function save()
    {
        try
        {
           $kencleng = Models\Kencleng::findOrFail($this->data['kencleng_id']);
           
        //    if ($kencleng->status == StatusKencleng::DISTRIBUTOR) 
        //         throw new Halt('Kencleng sudah didistribusikan');

        //     // Inisialisasi data distribusi kencleng
        //     // DIstributor ID dan status jadi distribusi
        //     $query = Models\DistribusiKencleng::create([
        //         'kencleng_id'       => $this->data['kencleng_id'],
        //         'distributor_id'    => $this->data['distributor_id'],
        //         'tgl_distribusi'    => now(),
        //         'status'            => 'distribusi',
        //     ]);

        //     if (!$query) throw new Halt('Gagal menyimpan data');

            $dataTampil = [
                'id'                => $kencleng->id,
                'no_kencleng'       => $kencleng->no_kencleng,
                'status'            => 'Distribusi',
                'batch'             => 'Batch ke-' . $kencleng->batchKenclengs->nama_batch,
                'waktu_distribusi'  => now(),
            ];

            // Simpan data kedalam array
            array_push($this->newDistribusi, $dataTampil);
        }
        catch (Halt $e)
        {
            Notification::make()
                ->title($e->getMessage() ?? 'Gagal menyimpan data')
                ->danger()
                ->send();
            return;
        }
        $this->form->fill(['distributor_id' => $this->data['distributor_id']]);

        Notification::make()
            ->title('Berhasil melakukan distribusi kencleng ke distributor')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.distribusi.scanner-to-distributor', [
            'newDistribusi' => $this->newDistribusi,
        ]);
    }
}
