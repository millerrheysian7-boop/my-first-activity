<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Implementing ShouldQueue is what makes this notification run in the
// background via Laravel's queue system (Part 6 / queue requirement)
// instead of blocking the request while the email sends.
class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->line("The product \"{$this->product->name}\" (SKU: {$this->product->sku}) is running low.")
            ->line("Current quantity: {$this->product->quantity}")
            ->line("Reorder level: {$this->product->reorder_level}")
            ->line('Please reorder this item soon.')
            ->action('View Product', route('products.show', $this->product));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'message' => "{$this->product->name} is low on stock ({$this->product->quantity} left).",
        ];
    }
}
