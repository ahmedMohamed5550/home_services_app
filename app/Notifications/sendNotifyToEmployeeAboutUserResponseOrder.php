<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendNotifyToEmployeeAboutUserResponseOrder extends Notification
{
    use Queueable;

    private $order_id;
    private $user_name;
    private $status;

    public function __construct($order_id,$user_name,$status)
    {
        $this->order_id = $order_id;
        $this->user_name = $user_name;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order_id,
            'user_name' => $this->user_name,
            'message' =>$this->user_name." " . $this->status . " this order",
            'timestamp' => Carbon::now()->toDateTimeString()
        ];
    }
}
