<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseOrder $purchaseOrder)
    {
    }

    public function build()
    {
        return $this->subject('Purchase Order Approved: ' . $this->purchaseOrder->reference_number)
            ->view('emails.purchase-order-approved')
            ->with(['purchaseOrder' => $this->purchaseOrder]);
    }
}
