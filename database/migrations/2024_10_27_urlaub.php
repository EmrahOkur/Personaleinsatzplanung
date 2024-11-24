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
        //
        Schema::create('urlaubs', function (Blueprint $table) {
            
            $table->id();
            $table->integer('verfügbare_tage')->default(30);
            $table->integer('genommene_tage')->default(0);     
            $table->integer('verplante_tage')->default(0);
            $table->integer('verbleibende_tage')->nullable();
            $table->string('abwesenheitsart')->nullable();
            $table->date('datum_start')->nullable();
            $table->date('datum_ende')->nullable();
            $table->time('zeit_start')->nullable();
            $table->time('zeit_ende')->nullable();
            $table->integer('gültigkeit')->nullable();
            $table->string('status')->nullable();
            $table->string('genehmigender')->nullable(); 
            $table->integer('kontingentverbrauch')->nullable();
            $table->text('zusatzinfo')->nullable();
            $table->json('selectedDates')->nullable();
            $table->timestamps();

           
        
       });
    } 
       public function down(): void
       {
           Schema::dropIfExists('urlaubs');
       }

    
    

    
};   