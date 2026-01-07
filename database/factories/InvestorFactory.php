<?php

namespace Database\Factories;

use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvestorFactory extends Factory
{
    protected $model = Investor::class;

    public function definition(): array
    {
        return [
            'investor_id' => fake()->unique()->numberBetween(1000, 99999),
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 80),
        ];
    }
}