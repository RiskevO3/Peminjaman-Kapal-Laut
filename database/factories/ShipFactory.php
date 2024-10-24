<?php

namespace Database\Factories;

use App\Models\Ship;
use App\Models\ShipCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ship>
 */
class ShipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalUnit = $this->faker->numberBetween(1, 100);
        $borrowedUnit = 0;
        $availableUnit = $totalUnit - $borrowedUnit;

        return [
            'ship_name' => $this->faker->word(),
            'total_unit' => $totalUnit,
            'borrowed_unit' => $borrowedUnit,
            'available_unit' => $availableUnit,
            'price' => $this->faker->numberBetween(1000, 5000),
            'penalty_fee' => $this->faker->numberBetween(100, 500),
        ];
    }

    /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Ship $ship) {
            $categoryIds = ShipCategory::pluck('id')->toArray();
            $randomCategoryId = $this->faker->randomElement($categoryIds);
            $ship->categories()->attach($randomCategoryId);
        });
    }
}