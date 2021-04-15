<?php

namespace Database\Factories;

use App\Models\Buyer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class BuyerFactory extends Factory
{
    public $refund_prefs = ['credit', 'cc'];
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Buyer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->unique()->name,
            'email' => $this->faker->unique()->email,
            'refund_pref' => $this->refund_prefs[rand(0, 1)],
        ];
    }
}
