<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Services\AddressService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        $addressService = app(AddressService::class);
        $adress = $this->splitStreetAddress($addressService->getRandom());

        return [
            'companyname' => $this->faker->company(),
            'vorname' => $this->faker->firstName(),
            'nachname' => $this->faker->lastName(),
            'street' => $adress['street'],
            'house_number' => $adress['house_number'],
            'zip_code' => $adress['zip_code'],
            'city' => $adress['city'],
            'customer_number' => $this->faker->unique()->numerify('K10###'),
        ];
    }

    public function splitStreetAddress($address)
    {
        if (preg_match('/^(.+?)\s*(\d.*)$/', $address['street'], $matches)) {
            return [
                'street' => trim($matches[1]),
                'house_number' => trim($matches[2]),
                'city' => $address['city'],
                'zip_code' => $address['zip_code'],
            ];
        }

        return [
            'street' => $address['street'],
            'house_number' => '',
            'city' => $address['city'],
            'zip_code' => $address['zip_code'],
        ];
    }
}
