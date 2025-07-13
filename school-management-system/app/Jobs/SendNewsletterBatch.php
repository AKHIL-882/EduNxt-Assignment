<?php

namespace App\Jobs;

use App\Mail\NewsletterEmail;
use App\Models\NewsletterLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNewsletterBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $batchEmails;
    public $content;
    public $tries = 5;

    public function backoff(): array
    {
        return [10, 30, 90, 180];
    }

    public function __construct(array $batchEmails, $content)
    {
        $this->batchEmails = $batchEmails;
        $this->content = $content;
    }

    public function handle()
    {
        foreach ($this->batchEmails as $email) {
            try {
                // Log email sending
                info("Sending newsletter to: {$email}");

                // Send email
                Mail::to($email)->send(new NewsletterEmail($this->content));

                // Log successful send
                NewsletterLog::create([
                    'email' => $email,
                    'status' => 'sent',
                    'error_message' => null
                ]);
            } catch (\Throwable $e) {
                // Log failure
                NewsletterLog::create([
                    'email' => $email,
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
        }
    }
}
