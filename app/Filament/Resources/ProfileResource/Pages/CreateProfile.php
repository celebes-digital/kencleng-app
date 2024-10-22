<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;

class CreateProfile extends CreateRecord
{
    protected static string $resource = ProfileResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->button()
                ->color('gray')
                ->url(ProfileResource::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        if($data['group'] === 'donatur' && $data['email'] === null) {
            $data['email'] = $data['nama'] . '@donatur.com';
        }

        $user = User::create([
            'email'      => $data['email'],
            'password'   => Hash::make('temp_password_key'),
        ]);

        $data['user_id'] = $user->id;
        $profile = static::getModel()::create($data);

        return $profile;
    }

    protected function afterCreate() {
        $user = User::find($this->record->user_id);
        $this->sendPasswordResetLink($user->email);
    }

    private function sendPasswordResetLink($email): void
    {
        $status = Password::sendResetLink(
            ['email' => $email]
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }


        Notification::make()
            ->title('Verifikasi Email Terkirim')
            ->body('Email verifikasi telah dikirim ke alamat email pengguna.')
            ->icon('heroicon-o-envelope')
            ->iconColor('success')
            ->send();
        return;
    }
}
