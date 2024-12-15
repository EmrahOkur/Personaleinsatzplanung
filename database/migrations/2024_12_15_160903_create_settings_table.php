<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('sidebar_visible')->default(false); // Der Schlüssel der Einstellung (z.B. 'sidebar_visible')
            $table->string('max_week_planning')->default(100); // Der Wert der Einstellung (z.B.  '7')
            $table->boolean('show_employees')->default(false); // Der Wert der Einstellung (z.B.  'true')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
