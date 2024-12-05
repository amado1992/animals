<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset url.
     *
     * @var string
     */
    public $url;

    /**
     * The user country language.
     *
     * @var string
     */
    public $userLanguage;

    /**
     * Create a new notification instance.
     *
     * @param  string  $url
     * @param  string  $userLanguage
     * @return void
     */
    public function __construct($url, $userLanguage)
    {
        $this->url          = $url;
        $this->userLanguage = $userLanguage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');*/

        if ($this->userLanguage === 'ES') {
            return (new MailMessage)
                ->from('izs@zoo-services.com', 'International Zoo Services')
                ->subject('Notificación de restablecimiento de contraseña')
                ->greeting('Hola, ' . $notifiable->name)
                ->line('Recibió este correo porque recibimos una solicitud de restablecimiento de contraseña para su cuenta. Haga clic en el botón de abajo para restablecer su contraseña:')
                ->action('Restablecer Contraseña', $this->url)
                ->line(new HtmlString('Este enlace de restablecimiento de contraseña caducará en ' . config('auth.passwords.users.expire') . ' minutos.<br><br>'))
                ->line(new HtmlString('Si no solicitó un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.<br><br>'))
                ->line('Si tiene problemas para hacer clic en el botón "Restablecer contraseña", copie y pegue la URL a continuación en su navegador web:');
        } else {
            return (new MailMessage)
                ->from('izs@zoo-services.com', 'International Zoo Services')
                ->subject('Reset Password Notification')
                ->greeting('Hello, ' . $notifiable->name)
                ->line('You are receiving this email because we received a password reset request for your account. Click the button below to reset your password:')
                ->action('Reset Password', $this->url)
                ->line(new HtmlString('This password reset link will expire in ' . config('auth.passwords.users.expire') . ' minutes.<br><br>'))
                ->line(new HtmlString('If you did not request a password reset, no further action is required.<br><br>'))
                ->line('If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
