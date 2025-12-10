<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactInquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactInquiry $inquiry;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactInquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('New Website Inquiry')
            ->markdown('emails.contact-admin', [
                'inquiry' => $this->inquiry,
            ]);
    }
}
