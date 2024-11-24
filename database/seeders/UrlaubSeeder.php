<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Urlaub;

class UrlaubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Urlaub::factory()->count(1)->create();
    }
}
