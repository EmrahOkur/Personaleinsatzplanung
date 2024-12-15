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
        Schema::create('addresses', function (Blueprint $table) {
            // Primärschlüssel
            $table->id();

            // Adressdaten
            $table->string('street');
            $table->string('house_number');
            $table->string('additional_info')->nullable(); // z.B. Etage, Apartment
            $table->string('zip_code');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country')->default('Deutschland');

            // System
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
