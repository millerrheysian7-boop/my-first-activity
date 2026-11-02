<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderSent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $purchaseOrder)
    {
    }

    public function build()
    {
        return $this->subject('Purchase Order ' . $this->purchaseOrder->reference_number . ' from BuildRight Hardware')
            ->view('emails.purchase-order-sent')
            ->with(['purchaseOrder' => $this->purchaseOrder]);
    }
}
