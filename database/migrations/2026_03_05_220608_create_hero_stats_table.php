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
    Schema::create('hero_stats', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('role');
        $table->float('win_rate');
        $table->float('pick_rate');
        $table->float('ban_rate');
        $table->string('tier'); // S, A, B, C
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_stats');
    }
};
