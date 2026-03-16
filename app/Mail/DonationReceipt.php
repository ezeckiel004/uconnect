<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationReceipt extends Mailable
{
    use SerializesModels;

    public $donation;

    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function build()
    {
        return $this->markdown('emails.donation-receipt')
            ->subject('Reçu de votre donation - U-Connect')
            ->from(env('MAIL_FROM_ADDRESS'), 'U-Connect');
    }
}
