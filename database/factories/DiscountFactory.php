<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Discount;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{

    protected $model = Discount::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('DISC????')), // Generates a random discount code
            'type' => $this->faker->randomElement(['percentage', 'fixed']), // Random type
            'value' => $this->faker->randomFloat(2, 5, 50), // Random discount value (5% to 50% or $5 to $50)
            'min_cart_total' => $this->faker->optional(0.5)->randomFloat(2, 20, 100), // Sometimes required, min $20-$100
            'applicable_products' => ['1', '2', '3'],
            'applicable_categories' => ['5', '9', '7'],
            'active_from' => Carbon::now()->subDays(rand(0, 30)), // Random start date in the past 30 days
            'active_to' => Carbon::now()->addDays(rand(5, 60)), // Random expiry date in the next 5-60 days
            'stackable' => $this->faker->boolean(30), // 30% chance it is stackable with other discounts
        ];
    }
}
