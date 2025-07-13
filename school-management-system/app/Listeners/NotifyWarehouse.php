<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyWarehouse
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
        // In real apps, you'd send a message, email, or trigger a warehouse API.
        \Log::info("Notify warehouse: Order #{$event->order->id} for product {$event->order->product_id}");
    }
}
