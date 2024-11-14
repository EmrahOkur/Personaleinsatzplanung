<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('break_duration')->default(0); // Dauer in Minuten
            $table->string('activity_type')->default('productive'); // Aktivitätstyp
            $table->timestamps();

            // Fremdschlüssel
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
};
