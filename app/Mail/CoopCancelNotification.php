<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoopCancelNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $coop;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($coop)
    {
        $this->coop = $coop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Coop has been canceled due by expiration date')
            ->view('mails.coop-canceled-by-expiration-date', [
                'coop' => $this->coop,
                'cancellation_reason' => 'by expiration date'
            ]);
    }
}
