<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function build(): self
    {
        return $this->subject('Your password was changed – ' . config('app.name'))
            ->markdown('emails.password-changed-user', [
                'user' => $this->user,
            ]);
    }
}
