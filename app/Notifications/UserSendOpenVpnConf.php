<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;

class UserSendOpenVpnConf extends Notification
{
    use Queueable;

    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

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
    public function toMail($notifiable)
    {
        $getThisFile = storage_path('app/' . $this->filePath);

        return (new MailMessage)      
                    ->subject('Download Ready')              
                    ->line('Some texts')
                    ->line('Thank you!')
                    ->attach($getThisFile);
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

    /**
 * Get the attachments for the message.
 *
 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
 */
public function attachments(): array
{
    dd(Attachment::fromStorage($this->filePath));
    return [
        Attachment::fromStorage($this->filePath)
    ];
}
}