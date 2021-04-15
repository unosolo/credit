<?php

use App\Models\Coop;

use App\Models\Purchase;
use App\Models\Transaction;

use App\Tasks\CancelExpiredCoops;

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


it('cancels expired coop with purchases with pending transaction', function () {
    $this->expectOutputString('');

    for($i = 0; $i < 5 ; $i++){
        Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])
            ]),
            'type' => 'purchase',
            'is_pending' => false,
            'is_canceled' => false
        ]);

        $transactions = Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])->id
            ])->id,
            'type' => 'purchase',
            'is_pending' => true,
            'is_canceled' => false
        ]);
    }


    $expired_coops = Coop::where('expiration_date', '<', date('Y-m-d'))->get();

    $expired_coops->map(function ($coop) {
        $coop->cancel();
    });

    $canceled_transactions = Transaction::where('is_canceled', true)->get();

    expect($canceled_transactions->count())->toBe(10);
});



it('cancels expired coop with purchases with pending credit card transactions', function () {
    $this->expectOutputString('');

    for($i = 0; $i < 5 ; $i++){
        Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])
            ]),
            'type' => 'purchase',
            'source' => 'CreditCard',
            'is_pending' => false,
            'is_canceled' => false
        ]);

        $transactions = Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])->id
            ])->id,
            'type' => 'purchase',
            'source' => 'CreditCard',
            'is_pending' => true,
            'is_canceled' => false
        ]);
    }


    $expired_coops = Coop::where('expiration_date', '<', date('Y-m-d'))->get();

    $expired_coops->map(function ($coop) {
        $coop->cancel();
    });

    $canceled_transactions = Transaction::where('is_canceled', true)->get();

    expect($canceled_transactions->count())->toBe(10);
});


it('tests CancelExpiredCoops Class', function () {
    $this->expectOutputString('');

    for($i = 0; $i < 5 ; $i++){
        Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])
            ]),
            'type' => 'purchase',
            'source' => 'CreditCard',
            'is_pending' => false,
            'is_canceled' => false
        ]);

        $transactions = Transaction::factory()->create([
            'purchase_id' =>  Purchase::factory()->create([
                'coop_id' => Coop::factory()->create([
                    'expiration_date' => now()->addWeeks(-2),
                ])->id
            ])->id,
            'type' => 'purchase',
            'source' => 'CreditCard',
            'is_pending' => true,
            'is_canceled' => false
        ]);
    }

    $cancelExpiredCoops = new App\Tasks\CancelExpiredCoops;
    $cancelExpiredCoops();

    $canceled_transactions = Transaction::where('is_canceled', true)->get();

    expect($canceled_transactions->count())->toBe(10);
});
