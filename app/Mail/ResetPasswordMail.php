<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $resetUrl;
    public string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl, string $token)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->token = $token;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Reset your password')
            ->markdown('emails.password-reset', [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                'token' => $this->token,
            ]);
    }
}
