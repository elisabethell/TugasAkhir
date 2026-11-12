<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_datasets', function (Blueprint $table) {
            $table->longText('map_summary')->nullable()->change();
        });

        Schema::table('match_games', function (Blueprint $table) {
            $table->longText('map_type')->nullable()->change();
        });

        Schema::table('match_datasets', function (Blueprint $table) {
            $table->longText('series_mvp')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('match_datasets', function (Blueprint $table) {
            $table->string('map_summary')->nullable()->change();
        });

        Schema::table('match_games', function (Blueprint $table) {
            $table->string('map_type', 100)->nullable()->change();
        });

        Schema::table('match_datasets', function (Blueprint $table) {
            $table->string('series_mvp')->nullable()->change();
        });
    }
};
