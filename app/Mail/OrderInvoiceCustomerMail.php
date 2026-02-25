<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function build(): self
    {
        return $this->subject('Order Confirmation – ' . $this->order->tracking_number)
            ->markdown('emails.order-invoice-customer', [
                'order' => $this->order,
            ]);
    }
}
