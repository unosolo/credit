<?php

use App\Models\Coop;

use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('cancels expired coop', function () {

    $coops = Coop::factory()->count(10)->create([
        'expiration_date' => now()->addWeeks(-2),
    ]);

    $expired_coops = Coop::where('expiration_date', '<', date('Y-m-d'))->get();

    $expired_coops->map(function ($coop) {
        $coop->cancel();
    });

    $canceled_coops = Coop::where('status', 'canceled')->get();

    expect($expired_coops->count())->toBe($canceled_coops->count());
});


it('cancels expired coop and its purchases', function () {

    $purchases = Purchase::factory()->count(20)->create([
       'coop_id' => Coop::factory()->create([
           'expiration_date' => now()->addWeeks(-2),
       ])
    ]);

    $expired_coops = Coop::where('expiration_date', '<', date('Y-m-d'))->get();

    $expired_coops->map(function ($coop) {
        $coop->cancel();
    });

    $canceled_purchases = Purchase::where('coop_canceled', '1')->get();

    expect($purchases->count())->toBe($canceled_purchases->count());
});
