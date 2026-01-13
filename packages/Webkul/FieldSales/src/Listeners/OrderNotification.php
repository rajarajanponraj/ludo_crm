<?php

namespace Webkul\FieldSales\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Webkul\FieldSales\Models\Order;

class OrderNotification
{
    /**
     * Handle the event.
     *
     * @param  \Webkul\FieldSales\Models\Order  $order
     * @return void
     */
    public function handle(Order $order)
    {
        Log::info("Order Created Event Fired for Order ID: {$order->id}");

        // Simulate SMS/Mail Logic
        // In real app: Mail::to($order->person->emails)->send(new OrderConfirmation($order));

        Log::info("Notification sent to customer: " . ($order->person->name ?? 'Unknown'));
    }
}
