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
        Schema::create('match_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_game_id')->constrained('match_games')->onDelete('cascade');
            $table->string('match_id');
            $table->unsignedTinyInteger('game_number');
            $table->string('team_name');
            $table->string('team_side', 10)->default('Blue'); // Blue / Red
            $table->unsignedTinyInteger('team_position')->default(1); // 1 = Team 1 (kiri), 2 = Team 2 (kanan)
            $table->enum('type', ['pick', 'ban']); // pick atau ban
            $table->unsignedTinyInteger('slot_number'); // 1 s/d 5
            $table->string('hero_name'); // Nama Hero, e.g. "Joy", "Freya"
            $table->string('hero_id')->nullable(); // Relasi opsional ke table heroes (hero_id string atau id)
            $table->text('hero_portrait')->nullable(); // URL portrait hero
            $table->timestamps();

            $table->index(['match_game_id', 'type']);
            $table->index(['hero_name', 'type']);
            $table->index(['team_name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_details');
    }
};
