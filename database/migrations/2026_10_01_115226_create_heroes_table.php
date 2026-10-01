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
    Schema::create('heroes', function (Blueprint $table) {
        $table->id();
        $table->string('hero_id')->unique(); // contoh: h001
        $table->string('hero_name');
        $table->text('portrait')->nullable(); // menyimpan URL foto
        $table->text('laning')->nullable();
        $table->string('class')->nullable();
        $table->longText('skills')->nullable();
        $table->text('specialty')->nullable();
        $table->longText('counters')->nullable();
        $table->longText('synergies')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
