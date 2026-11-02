<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PurchaseOrdersGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $orders)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'count' => count($this->orders),
            'references' => collect($this->orders)->pluck('reference_number')->toArray(),
            'message' => count($this->orders) . ' draft purchase order(s) were auto-generated from the scheduled reorder check.',
        ];
    }
}
