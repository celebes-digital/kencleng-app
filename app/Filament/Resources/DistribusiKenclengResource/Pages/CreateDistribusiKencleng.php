<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

use App\Models\Kencleng;
use App\Filament\Resources\DistribusiKenclengResource;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDistribusiKencleng extends CreateRecord
{
    protected static string $resource = DistribusiKenclengResource::class;

    protected function beforeCreate()
    {
        $kencleng = Kencleng::where('no_kencleng', $this->data['scanner'])->first();
        
        if($kencleng->status == 1 ) {
            Notification::make()
                ->title('Kencleng ' . $this->data['scanner'] . ' sedang didistribusikan')
                ->danger()
                ->send();

            $this->halt();
        } 
        $kencleng->status = 1;
        $kencleng->save();
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
}
