<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addressService = app(AddressService::class);
        $addresses = $addressService->getAddresses();

        foreach ($addresses as $a) {
            Address::create([
                'street' => $a['street'],
                'house_number' => $a['house_number'],
                'zip_code' => $a['zip_code'],
                'city' => $a['city'],
            ]);
        }
    }
}
