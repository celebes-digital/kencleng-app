<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;

use App\Models\DistribusiKencleng;
use Filament\Actions\Action;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\ViewAction;

class JadwalKoleksiCalenderWidget extends FullCalendarWidget
{
    public Model | string | null $model = DistribusiKencleng::class;

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'fixedWeekCount' => false,
            'headerToolbar' => [
                'left' => 'dayGridMonth,dayGridWeek,dayGridDay',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
            'dayMaxEventRows' => true,
            'views' => [
                'dayGridMonth' => [
                    'dayMaxEventRows' => 5,
                    'moreLinkClick' => 'day',

                ]
            ]
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return DistribusiKencleng::query()
            ->whereBetween('tgl_distribusi', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->get()
            ->map(
                function (DistribusiKencleng $event) {
                    return EventData::make()
                        ->id($event->id)
                        ->title($event->kencleng->no_kencleng)
                        ->start(
                            \Carbon\Carbon::parse($event->tgl_distribusi)->addMonthWithoutOverflow()->format('Y-m-d')
                        )
                        ->allDay(true)
                        ->end(
                            \Carbon\Carbon::parse($event->tgl_distribusi)->addMonthWithoutOverflow()->format('Y-m-d')
                        )
                        ->backgroundColor(
                            $event->status->value !== 'diterima' 
                                ? (\Carbon\Carbon::parse($event->tgl_distribusi)->addMonth()->isPast() 
                                    ? '#dc2626' // Red for past dates after one month from tgl_distribusi
                                    : (\Carbon\Carbon::parse($event->tgl_distribusi)->addDays(23)->isPast() 
                                        ? '#f59e0b' // Warning color for dates within 7 days before 30 days after tgl_distribusi
                                        : '#3b82f6' // Info color for other cases
                                    )
                                ) 
                                : '' // Primary color if status is 'diterima'
                        )
                        ->borderColor($event->status->value !== 'diterima'? '#fbeed3' : '')
                        ->textColor('#fff')
                        // ->backgroundColor($event->status->value !== 'diterima' ? '#d97706' : '')
                        // ->borderColor($event->status->value !== 'diterima'? '#fbeed3' : '')
                        // ->textColor('#fff')
                        ;
                    // ->url(
                    //     url: EventResource::getUrl(name: 'view', parameters: ['record' => $event]),
                    //     shouldOpenUrlInNewTab: true
                    // )
                }
            )
            ->toArray();
    }


    protected function headerActions(): array
    {
        return [];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->infolist([
                Fieldset::make('Data Distribusi')
                    ->schema([
                        TextEntry::make('kencleng.no_kencleng'),
                        TextEntry::make('tgl_distribusi')
                            ->icon('heroicon-o-calendar-days')
                            ->label('Tanggal Distribusi'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('donatur.nama'),
                        TextEntry::make('distributor.nama'),
                        TextEntry::make('kolektor.nama'),
                    ])
                    ->columns(3),

                Fieldset::make('Detail Distribusi')
                    ->schema([
                        TextEntry::make('donatur.alamat')
                            ->label('Alamat Donatur'),
                        TextEntry::make('donatur.no_wa')
                            ->label('Whatsapp Donatur'),
                        Actions::make([
                            InfoAction::make('buka_maps')
                                ->modalDescription('Buka Map di Tab Baru')
                                ->requiresConfirmation()
                                ->url(fn($record) => 'https://www.google.com/maps/search/?api=1&query='
                                    . $record->geo_lat
                                    . ','
                                    . $record->geo_long,)
                                ->openUrlInNewTab()
                        ]),
                    ])
                    ->columns(3)
            ]);
    }

    public function eventDidMount(): string
    {
        return <<<JS
        function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }){
            el.setAttribute("x-tooltip", "tooltip");
            el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
            el.style.cursor = "pointer";
        }
    JS;
    }
}
