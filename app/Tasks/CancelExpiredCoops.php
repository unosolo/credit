<?php


namespace App\Tasks;


use App\Models\Coop;

class CancelExpiredCoops
{
    public function __invoke()
    {
        $expired_coops = Coop::where('expiration_date', '<', date('Y-m-d'))->get();

        $expired_coops->map(function ($coop) {
            $coop->cancel();
        });
    }
}
