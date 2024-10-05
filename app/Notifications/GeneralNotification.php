<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $title,$message,$sourceable_id,$web_link,$sourceable_type;

    /**
     * Create a new notification instance.
     */
    public function __construct($title,$message,$sourceable_id,$web_link,$sourceable_type)
    {
        $this->title=$title;
        $this->message=$message;
        $this->sourceable_id=$sourceable_id;
        $this->sourceable_type=$sourceable_type;
        $this->web_link=$web_link;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'sourceable_id' => $this->sourceable_id,
            'sourceable_type' => $this->title,
            'web_link' => $this->web_link,

        ];
    }
}
