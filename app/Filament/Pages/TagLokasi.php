<?php

namespace App\Filament\Pages;

use App\Filament\Components\ScannerQrCode;
use App\Models\DistribusiKencleng;
use App\Models\Kencleng;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Support\Exceptions\Halt;

class TagLokasi extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static string $view = 'filament.pages.tag-lokasi';
    protected static ?string $modelLabel        = 'Tag Lokasi';
    protected static ?string $label             = 'Tag Lokasi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'tag-lokasi';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
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

                        $set('kencleng_id', $state);
                    }),
                Fieldset::make('')
                    ->schema([
                        TextInput::make('kencleng_id')
                            ->label('No. Kencleng')
                            ->disabled()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->disabled(),
                        TextInput::make('longitude')
                            ->disabled(),
                    ])
            ])
            ->from('md'),
        ])
            ->statePath('data')
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }


    public function checkNoKencleng($noKencleng)
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

    public function save()
    {
        try {
            $distribusiKencleng = DistribusiKencleng::whereHas('kencleng', function ($query) {
                $query->where('no_kencleng', $this->data['kencleng_id']);
            })->whereNull('tgl_distribusi')->first();

            if (empty($distribusiKencleng)) {
                Notification::make()
                    ->title('Distribusi Kencleng tidak ditemukan atau belum didistribusikan')
                    ->danger()
                    ->send();
                return;
            }

            $distribusiKencleng->update([
                'tgl_distribusi' => now(),
                'geo_lat'        => $this->data['latitude'],
                'geo_long'       => $this->data['longitude'],
            ]);
            
        } catch (Halt $e) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
