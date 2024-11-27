<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('break_duration')->nullable();  // Hier ist break_duration nullable gesetzt
            $table->enum('activity_type', ['productive', 'non-productive'])->default('productive'); // Aktivitätstyp
            $table->timestamps();

            // Fremdschlüssel
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            // Index
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
