<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    /**
     * Send the order confirmation email to the customer in the background.
     */
    public function handle(): void
    {
        $order = $this->order->load(['customer', 'items']);

        $lines = $order->items
            ->map(fn ($item) => $item->quantity.' x '.$item->product_name.' @ PHP '.number_format((float) $item->price, 2))
            ->implode("\n");

        $body = "Hi {$order->customer->name},\n\n"
            ."Thank you for your order at TZ Carshop.\n\n"
            ."Order #{$order->id}\n{$lines}\n\n"
            .'Total: PHP '.number_format((float) $order->total, 2)."\n"
            ."Status: {$order->status_label}";

        Mail::raw($body, function ($message) use ($order) {
            $message->to($order->customer->email)
                ->subject('TZ Carshop Order #'.$order->id.' Confirmation');
        });
    }
}
