<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAnalytics
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event)
    {
        \Log::info("Order Placed", [
            'order_id' => $event->order->id,
            'email' => $event->order->email,
            'time' => now(),
        ]);
    }
}
