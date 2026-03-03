<?php

namespace App\Mail;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WithdrawalRejectedMail extends Mailable
{
    public function __construct(
        public WithdrawalRequest $withdrawalRequest,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Votre demande de retrait a été rejetée',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal_rejected',
            with: [
                'withdrawalRequest' => $this->withdrawalRequest,
                'user' => $this->user,
            ]
        );
    }
}
