<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TimeEntry;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Erstellen von 50 zufälligen Zeiteinträgen
        TimeEntry::factory()->count(50)->create();
    }
}
