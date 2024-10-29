<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\DistribusiKencleng;
use Illuminate\Database\Eloquent\Model;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\ViewAction;
use Saade\FilamentFullCalendar\Data\EventData;

use Filament\Actions\Action;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Auth;

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
        $query = DistribusiKencleng::query();

        $user = Auth::user();

        if(!$user->is_admin && $user->profile->group === 'kolektor') {
            $query->where('kolektor_id', $user->profile->id);
        }
        
        if(!$user->is_admin && $user->profile->group === 'distributor') {
            $query->where('distributor_id', $user->profile->id);
        }

        return $query
            ->whereBetween(
                'tgl_distribusi', 
                [
                    Carbon::parse($fetchInfo['start'])->subMonthNoOverflow(),
                    Carbon::parse($fetchInfo['end'])->subMonthNoOverflow()
                ]
            )
            ->select('distribusi_kenclengs.*', 'kenclengs.no_kencleng')
            ->join('kenclengs', 'distribusi_kenclengs.kencleng_id', '=', 'kenclengs.id')
            ->get()
            ->map(
                function (DistribusiKencleng $event) 
                {
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
                            $this->getColorByStatus($event, true)
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
                                    return $this->getColorByStatus($record);
                                }
                            )
                            ->formatStateUsing(
                                function ($state, $record) 
                                {
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

    private function getColorByStatus(DistribusiKencleng $event, bool $isHex = false): string
    {
        $deadline = Carbon::parse($event->tgl_distribusi)
            ->addMonthWithoutOverflow()
            ->addDay(1);

        $color = $isHex 
                    ? ['', '#dc2626', '#f59e0b', '#3b82f6'] 
                    : ['primary', 'danger', 'warning', 'info'];

        if ($event->status->value === 'diterima') {
            return $color[0];
        }

        if ($deadline->isPast()) {
            return $color[1];
        }

        if ($deadline->subDays(7)->isPast()) {
            return $color[2];
        }

        return $color[3];
    }
}
