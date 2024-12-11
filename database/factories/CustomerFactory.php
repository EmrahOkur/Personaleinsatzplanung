<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Services\AddressService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        

        return [
            'companyname' => $this->faker->company(),
            'vorname' => $this->faker->firstName(),
            'nachname' => $this->faker->lastName(),
            'address_id' => Address::inRandomOrder()->first(),
            'customer_number' => $this->faker->unique()->numerify('K10###'),
        ];
    }

    
}
