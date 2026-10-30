<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Order Approved: ' . $this->purchaseOrder->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-order-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}