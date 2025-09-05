<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesPerson>
 */
class SalesPersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
               'firstname'  => $this->faker->firstName(),
            'lastname'   => $this->faker->lastName(),
            'middlename' => $this->faker->firstName(), 
            'address'    => $this->faker->address(),
            'contact'    => $this->faker->phoneNumber(),
        ];
    }
}
