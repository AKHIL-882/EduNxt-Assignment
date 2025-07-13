<?php

namespace App\Jobs;

use App\Mail\ResetPasswordLinkMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ProcessResetPasswordMailJob implements ShouldQueue
{
    use Queueable;

    public $token;

    public $email;

    public $expiry;

    public $name;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $token, $expiry, $name)
    {
        $this->email = $email;
        $this->expiry = $expiry;
        $this->name = $name;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new ResetPasswordLinkMail($this->email, $this->name, $this->token, $this->expiry));
    }
}