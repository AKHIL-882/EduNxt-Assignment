<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemHealthNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('System Health Notification')
                    ->view('emails.system_health');
    }
}