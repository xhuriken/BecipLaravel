<?php

namespace App\Notifications;

use http\Client\Curl\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordToken extends Notification
{
    use Queueable;

    public $token;

    /**
     * Constructeur : on récupère le $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Canaux de notification (ici, email)
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Construire le mail avec un Blade perso
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->view('emails.reset_password', [
                'user' => $notifiable,
                'url'  => $resetUrl,
            ]);
    }
}
