<?php

use App\Models\Coop;

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

  assertTrue($expired_coops === $canceled_coops);
});
