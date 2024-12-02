<?php

declare(strict_types=1);
// database/migrations/2024_11_24_create_availabilities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('weekday'); // 1 = Montag, 7 = Sonntag
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // Index für schnellere Abfragen
            $table->index(['employee_id', 'weekday']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('availabilities');
    }
};
