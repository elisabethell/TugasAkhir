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
        Schema::create('datasets', function (Blueprint $table) {
            $table->id('id_record'); // Primary Key
            
            // Kolom berdasarkan Data Dictionary MWI_X_EWC_2026.csv
            $table->date('date');
            $table->string('side', 10);
            $table->string('win_lose', 10);
            $table->string('player', 50);
            $table->string('role', 20);
            $table->string('hero', 50);
            $table->text('hero_ban')->nullable(); 
            $table->string('spell', 50);
            $table->string('team', 50);
            $table->string('opponent', 50);
            $table->string('skor_game', 10);
            $table->integer('kill_stat');
            $table->integer('death_stat');
            $table->integer('assist_stat');
            $table->string('duration', 10);
            $table->string('map', 50);
            $table->string('bracket', 50);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
