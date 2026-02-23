<?php

namespace App\Mail;

use App\Models\Cagnote;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CagnoteRejectedMail extends Mailable
{

    public function __construct(
        public Cagnote $cagnote,
        public User $association,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cagnote refusée - Raison de rejet',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cagnote_rejected',
            with: [
                'cagnote' => $this->cagnote,
                'association' => $this->association,
                'reason' => $this->reason,
            ]
        );
    }
}
