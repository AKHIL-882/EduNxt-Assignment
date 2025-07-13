<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Jobs\SendNewsletterBatch;

class NewsletterController extends Controller
{
    public function send(Request $request)
    {
        $content = $request->input('content');
        $emails = Subscriber::pluck('email')->toArray();
        $chunks = array_chunk($emails, 100);

        foreach ($chunks as $chunk) {
            SendNewsletterBatch::dispatch($chunk, $content);
        }

        return response()->json(['message' => 'Newsletter queued for sending']);
    }
}