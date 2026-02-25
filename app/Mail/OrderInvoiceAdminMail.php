<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function build(): self
    {
        return $this->subject('New Order #' . $this->order->tracking_number)
            ->markdown('emails.order-invoice-admin', [
                'order' => $this->order,
            ]);
    }
}
