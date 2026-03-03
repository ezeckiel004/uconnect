<?php

namespace App\Mail;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequestCreatedAssociation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public WithdrawalRequest $withdrawalRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(WithdrawalRequest $withdrawalRequest)
    {
        $this->withdrawalRequest = $withdrawalRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Virement en cours pour votre cagnote : ' . $this->withdrawalRequest->cagnote->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-request-association',
            with: [
                'withdrawalRequest' => $this->withdrawalRequest,
                'cagnote' => $this->withdrawalRequest->cagnote,
                'user' => $this->withdrawalRequest->user,
                'originalAmount' => $this->withdrawalRequest->original_amount,
                'withdrawalAmount' => $this->withdrawalRequest->withdrawal_amount,
                'platformFee' => $this->withdrawalRequest->platform_fee,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
