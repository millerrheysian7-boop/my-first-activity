<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $purchaseOrder)
    {
    }

    public function build()
    {
        return $this->subject('New Purchase Order Created: ' . $this->purchaseOrder->reference_number)
            ->view('emails.purchase-order-created')
            ->with(['purchaseOrder' => $this->purchaseOrder]);
    }
}
