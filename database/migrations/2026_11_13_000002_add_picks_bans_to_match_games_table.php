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
        Schema::table('match_games', function (Blueprint $table) {
            if (!Schema::hasColumn('match_games', 'winner')) {
                $table->string('winner')->nullable()->after('map_type');
            }
            if (!Schema::hasColumn('match_games', 'team_1_picks')) {
                $table->longText('team_1_picks')->nullable()->after('team_2_side');
            }
            if (!Schema::hasColumn('match_games', 'team_2_picks')) {
                $table->longText('team_2_picks')->nullable()->after('team_1_picks');
            }
            if (!Schema::hasColumn('match_games', 'team_1_bans')) {
                $table->longText('team_1_bans')->nullable()->after('team_2_picks');
            }
            if (!Schema::hasColumn('match_games', 'team_2_bans')) {
                $table->longText('team_2_bans')->nullable()->after('team_1_bans');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            $cols = [];
            foreach (['winner', 'team_1_picks', 'team_2_picks', 'team_1_bans', 'team_2_bans'] as $col) {
                if (Schema::hasColumn('match_games', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

