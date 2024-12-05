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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('restrict');
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->onDelete('restrict');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->timestamps();
            $table->softDeletes();

            $table->index('appointment_date');
            $table->index('appointment_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
