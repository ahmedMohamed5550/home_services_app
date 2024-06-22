<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendNotifyToUserAboutEmployeeResponse extends Notification
{
    use Queueable;

    private $order_id;
    private $employee_name;
    private $status;

    public function __construct($order_id,$employee_name,$status)
    {
        // $this->id = $id;
        $this->order_id = $order_id;
        $this->employee_name = $employee_name;
        $this->status = $status;
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


    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order_id,
            'employee_name' => $this->employee_name,
            'message' =>$this->employee_name." " . $this->status . " this order",
        ];
    }
}
