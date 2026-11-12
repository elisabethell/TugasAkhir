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
        Schema::create('match_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_dataset_id')->constrained('match_datasets')->onDelete('cascade');
            $table->string('match_id'); // Relasi string ke match_datasets.match_id
            $table->unsignedTinyInteger('game_number'); // 1, 2, 3, 4, 5
            $table->string('duration', 20)->nullable(); // contoh: "14:45", "18:41"
            $table->longText('map_type')->nullable(); // contoh: "Healing Turtle", "Revealing Wisps"
            $table->string('winner_team')->nullable(); // Nama tim pemenang game
            $table->enum('team_1_status', ['W', 'L'])->default('L'); // W = Menang, L = Kalah
            $table->enum('team_2_status', ['W', 'L'])->default('L'); // W = Menang, L = Kalah
            $table->string('team_1_side', 10)->default('Blue'); // Blue atau Red
            $table->string('team_2_side', 10)->default('Red');  // Blue atau Red
            $table->timestamps();

            $table->unique(['match_id', 'game_number']);
            $table->index('match_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_games');
    }
};
