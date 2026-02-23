<?php

namespace App\Mail;

use App\Models\Cagnote;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CagnoteCreatedAdminMail extends Mailable
{

    public function __construct(
        public Cagnote $cagnote,
        public User $association
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle cagnote à valider - ' . $this->cagnote->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cagnote_created_admin',
            with: [
                'cagnote' => $this->cagnote,
                'association' => $this->association,
                'reviewUrl' => route('admin.cagnotes.review', $this->cagnote->id),
            ]
        );
    }
}
