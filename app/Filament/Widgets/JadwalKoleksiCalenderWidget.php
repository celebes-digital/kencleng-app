<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;

use App\Models\DistribusiKencleng;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\EditAction;
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
        // dd($fetchInfo);
        return DistribusiKencleng::query()
            ->whereBetween('tgl_distribusi', [
                Carbon::parse($fetchInfo['start'])->subMonthNoOverflow(),
                Carbon::parse($fetchInfo['end'])->subMonthNoOverflow()
            ])
            ->select('distribusi_kenclengs.*', 'kenclengs.no_kencleng')
            ->join('kenclengs', 'distribusi_kenclengs.kencleng_id', '=', 'kenclengs.id')
            ->get()
            ->map(
                function (DistribusiKencleng $event) {
                    $nextMonth = Carbon::parse($event->tgl_distribusi)
                        ->addMonthWithoutOverflow()
                        ->format('Y-m-d');

                    return EventData::make()
                        ->id($event->id)
                        ->title($event->no_kencleng)
                        ->start($nextMonth)
                        ->allDay(true)
                        ->end($nextMonth)
                        ->backgroundColor(
                            $this->getEventBackgroundColor(
                                $event,
                                Carbon::parse($event->tgl_distribusi)
                            )
                        )
                        ->borderColor($event->status->value !== 'diterima' ? '#fbeed3' : '')
                        ->textColor('#fff');
                }
            )
            ->toArray();
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Distribusi')
        ];
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
                            ->formatStateUsing(
                                fn($state) => Carbon::parse($state)->translatedFormat('j F Y')
                            )
                            ->label('Tanggal Distribusi'),

                        TextEntry::make('tgl_distribusi')
                            ->label('Status Jadwal')
                            ->icon('heroicon-o-calendar')
                            ->badge()
                            ->color(
                                function ($record) {
                                    $deadline = Carbon::parse($record->tgl_distribusi)
                                        ->addMonthWithoutOverflow()
                                        ->addDay(1);

                                    if ($record->status->value === 'diterima') {
                                        return 'primary';
                                    }

                                    if ($deadline->isPast()) {
                                        return 'danger';
                                    }

                                    if ($deadline->subDays(7)->isPast()) {
                                        return 'warning';
                                    }

                                    return 'info';
                                }
                            )
                            ->formatStateUsing(
                                function ($state, $record) {
                                    $deadline = Carbon::parse($state)
                                        ->addMonthWithoutOverflow()
                                        ->addDay(1);

                                    if ($record->status->value === 'diterima') {
                                        return 'Selesai';
                                    }

                                    if ($deadline->isPast()) {
                                        return 'Lewat deadline';
                                    }

                                    return $deadline->diffForHumans();
                                }
                            ),

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
                                ->url(
                                    fn($record)
                                    => 'https://www.google.com/maps/search/?api=1&query='
                                        . $record->geo_lat
                                        . ','
                                        . $record->geo_long,
                                )
                                ->openUrlInNewTab()
                        ]),
                    ])
                    ->columns(3)
            ]);
    }

    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }) {
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
                el.style.cursor = "pointer";
            }
        JS;
    }

    private function getEventBackgroundColor(DistribusiKencleng $event, Carbon $distribusiDate): string
    {
        if ($event->status->value === 'diterima') {
            return ''; // Primary color for 'diterima'
        }

        if ($distribusiDate->copy()->addMonth()->isPast()) {
            return '#dc2626'; // Red for past dates
        }

        if ($distribusiDate->copy()->addDays(23)->isPast()) {
            return '#f59e0b'; // Warning color
        }

        return '#3b82f6'; // Default color
    }
}
