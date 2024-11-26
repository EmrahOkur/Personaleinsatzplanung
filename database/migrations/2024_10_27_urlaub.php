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

        Schema::create('urlaubs', function (Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('datum')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urlaubs');
    }
};
