<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $token) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Reset Password to Verify Email')
                    ->greeting('Assalamualaikum Wr. Wb. ' . $notifiable->profile->nama)
                    ->line('Email ini dikirimkan untuk memverifikasi akun Anda karena telah mendaftarkan diri sebagai ' . $notifiable->profile->group . ' di Aplikasi Kencleng Jariyah')
                    ->action('Mulai', $this->resetUrl($notifiable))
                    ->line('Link ini akan kadaluarsa dalam :count menit.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')])
                    ->line('Jika Anda tidak merasa melakukan tindakan ini, abaikan email ini.');
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
