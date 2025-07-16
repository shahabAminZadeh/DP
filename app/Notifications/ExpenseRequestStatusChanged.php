<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseRequestStatusChanged extends Notification
{
    use Queueable;

    public $status;
    public $reason;

    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

     public function toMail($notifiable)
    {
        $subject = $this->status == 'approved'
            ? 'درخواست هزینه شما تایید شد'
            : 'درخواست هزینه شما رد شد';

        $message = $this->status == 'approved'
            ? 'درخواست هزینه شما با موفقیت تایید شد و در نوبت پرداخت قرار گرفت.'
            : 'متاسفانه درخواست هزینه شما رد شد. دلیل: ' . $this->reason;

        return (new MailMessage)
                    ->subject($subject)
                    ->line($message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
