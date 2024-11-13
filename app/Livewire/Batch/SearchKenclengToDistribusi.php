<?php

namespace App\Livewire\Batch;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class SearchKenclengToDistribusi extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('batch')
                    ->placeholder('Batch ke-')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Select::make('distributor_id')
                    ->placeholder('Distibutor ID')
                    ->options([
                        '1' => 'Distributor 1',
                        '2' => 'Distributor 2',
                        '3' => 'Distributor 3',
                    ])
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('no_kencleng')
                    ->placeholder('Distribusi ke-')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
            ])
            ->columns([
                'md' => 5,
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.batch.search-kencleng-to-distribusi');
    }
}