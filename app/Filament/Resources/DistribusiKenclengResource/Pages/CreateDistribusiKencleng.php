<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

use App\Filament\Resources\DistribusiKenclengResource;
use App\Models\Kencleng;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDistribusiKencleng extends CreateRecord
{
    protected static string $resource = DistribusiKenclengResource::class;
    
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
}
