<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendNotifyToEmployeeAboutOrder extends Notification
{
    use Queueable;

    private $order_id;
    private $location;
    private $date_of_delivery;
    private $descriptions;
    private $user_name;

    public function __construct($order_id,$location,$date_of_delivery,$descriptions,$user_name)
    {
        $this->order_id = $order_id;
        $this->location = $location;
        $this->date_of_delivery = $date_of_delivery;
        $this->descriptions = $descriptions;
        $this->user_name = $user_name;
    }


    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order_id,
            'location' => $this->location,
            'date_of_delivery' => $this->date_of_delivery,
            'descriptions' => $this->descriptions,
            'user_name' => $this->user_name,
            'timestamp' => Carbon::now()->toDateTimeString()
        ];
    }
}
