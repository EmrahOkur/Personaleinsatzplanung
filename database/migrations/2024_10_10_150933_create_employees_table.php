<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Persönliche Daten
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->date('birth_date')->nullable();

            // Mitarbeiterdaten
            $table->string('employee_number')->unique();
            $table->date('hire_date');
            $table->date('exit_date')->nullable();
            $table->string('position')->nullable();
            $table->integer('vacation_days')->default(30);
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->decimal('working_hours')->default(40);

            // System
            $table->timestamps();
            $table->softDeletes(); // Für logisches Löschen

            $table->index(['last_name', 'first_name']);
            $table->index('email');
            $table->index('employee_number');

            // Beziehung
            $table->foreignId('address_id');

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
