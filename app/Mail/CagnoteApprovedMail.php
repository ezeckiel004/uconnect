<?php

namespace App\Mail;

use App\Models\Cagnote;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CagnoteApprovedMail extends Mailable
{

    public function __construct(
        public Cagnote $cagnote,
        public User $association
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cagnote acceptée - Maintenant en ligne!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cagnote_approved',
            with: [
                'cagnote' => $this->cagnote,
                'association' => $this->association,
            ]
        );
    }
}
