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
        if (!Schema::hasTable('heroes')) {
            return;
        }

        Schema::table('heroes', function (Blueprint $table) {
            if (!Schema::hasColumn('heroes', 'role_1')) {
                $table->string('role_1')->nullable()->after('class');
            }
            if (!Schema::hasColumn('heroes', 'role_2')) {
                $table->string('role_2')->nullable()->after('role_1');
            }
            if (!Schema::hasColumn('heroes', 'lane_1')) {
                $table->string('lane_1')->nullable()->after('role_2');
            }
            if (!Schema::hasColumn('heroes', 'lane_2')) {
                $table->string('lane_2')->nullable()->after('lane_1');
            }
            if (!Schema::hasColumn('heroes', 'sustainability')) {
                $table->unsignedTinyInteger('sustainability')->default(5)->after('lane_2');
            }
            if (!Schema::hasColumn('heroes', 'attack_power')) {
                $table->unsignedTinyInteger('attack_power')->default(5)->after('sustainability');
            }
            if (!Schema::hasColumn('heroes', 'crowd_control')) {
                $table->unsignedTinyInteger('crowd_control')->default(5)->after('attack_power');
            }
            if (!Schema::hasColumn('heroes', 'mobility')) {
                $table->unsignedTinyInteger('mobility')->default(5)->after('crowd_control');
            }
            if (!Schema::hasColumn('heroes', 'lane_control')) {
                $table->unsignedTinyInteger('lane_control')->default(5)->after('mobility');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('heroes')) {
            return;
        }

        $columns = [
            'role_1',
            'role_2',
            'lane_1',
            'lane_2',
            'sustainability',
            'attack_power',
            'crowd_control',
            'mobility',
            'lane_control',
        ];

        $existing = array_values(array_filter($columns, fn (string $col) => Schema::hasColumn('heroes', $col)));

        if (!empty($existing)) {
            Schema::table('heroes', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
