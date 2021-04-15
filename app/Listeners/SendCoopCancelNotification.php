<?php

namespace App\Listeners;

use App\Mail\CoopCancelNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCoopCancelNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $coop = $event->coop;
        $to = $coop->owner->email;

        Mail::to($to)
            ->send(new CoopCancelNotification($coop));
    }
}
