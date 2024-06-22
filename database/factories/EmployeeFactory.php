<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'desc' => $this->faker->text,
            'min_price' => $this->faker->randomDigit,
            'imageSSN' => $this->faker->image,
            'user_id' => 2,
            'service_id' =>1,
        ];
    }
}
