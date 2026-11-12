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
        Schema::create('match_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('match_id')->unique(); // match2id asli dari Liquipedia
            $table->string('tournament_name')->default('MPL Indonesia Season 18');
            $table->dateTime('match_date')->nullable(); // Waktu terkonversi ke WIB
            $table->string('team_1_name');
            $table->string('team_2_name');
            $table->text('team_1_logo')->nullable();
            $table->text('team_2_logo')->nullable();
            $table->unsignedTinyInteger('team_1_score')->default(0);
            $table->unsignedTinyInteger('team_2_score')->default(0);
            $table->string('best_of', 20)->default('Bo3'); // Bo3, Bo5, Bo7
            $table->string('stage_name')->nullable(); // Regular Season, Lower Bracket Final, dll.
            $table->longText('map_summary')->nullable(); // Ringkasan varian map seri pertandingan
            $table->longText('series_mvp')->nullable(); // Dari extradata.mvp
            $table->longText('raw_payload')->nullable(); // Simpan raw JSON response untuk audit data
            $table->timestamps();

            $table->index('match_date');
            $table->index(['team_1_name', 'team_2_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_datasets');
    }
};
