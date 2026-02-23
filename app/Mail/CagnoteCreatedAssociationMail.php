<?php

namespace App\Mail;

use App\Models\Cagnote;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CagnoteCreatedAssociationMail extends Mailable
{

    public function __construct(
        public Cagnote $cagnote,
        public User $association
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre cagnote a été reçue - Analyse en cours',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cagnote_created_association',
            with: [
                'cagnote' => $this->cagnote,
                'association' => $this->association,
            ]
        );
    }
}
