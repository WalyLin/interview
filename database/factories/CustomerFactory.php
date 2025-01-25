<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;


class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    protected $casts = [
        'dob' => 'datetime:Y-m-d',
        'creation_date' => 'datetime:Y-m-d H:i:s',
    ];
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'age' => $this->faker->numberBetween(18, 99),
            'dob' => $this->faker->date('Y-m-d'),
            'creation_date' => $this->faker->dateTime,
        ];
    }
}