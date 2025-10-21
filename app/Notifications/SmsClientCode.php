<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Log;

class SmsClientCode extends Notification
{
    use Queueable;

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['log'];
    }

    public function toLog($notifiable)
    {
        Log::info("SMS to {$notifiable->telephone}: Your security code is {$this->code}");
        return ['message' => "SMS sent: {$this->code}"];
    }
}
