<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('rbr_rules')) {
            Schema::create('rbr_rules', function (Blueprint $table) {
                $table->id('id_rule');
                $table->string('rule_code', 50)->unique();
                $table->string('category', 50)->default('Synergy'); // Synergy, Counter, PowerSpike, Composition
                $table->text('if_condition');
                $table->text('then_recommendation');
                $table->decimal('confidence_weight', 4, 2)->default(0.85);
                $table->text('description')->nullable();
                $table->boolean('status_aktif')->default(1);
                $table->timestamps();
            });
        } else {
            Schema::table('rbr_rules', function (Blueprint $table) {
                if (!Schema::hasColumn('rbr_rules', 'rule_code')) {
                    $table->string('rule_code', 50)->nullable()->after('id_rule');
                }
                if (!Schema::hasColumn('rbr_rules', 'category')) {
                    $table->string('category', 50)->default('Synergy')->after('rule_code');
                }
                if (!Schema::hasColumn('rbr_rules', 'if_condition')) {
                    $table->text('if_condition')->nullable()->after('category');
                }
                if (!Schema::hasColumn('rbr_rules', 'then_recommendation')) {
                    $table->text('then_recommendation')->nullable()->after('if_condition');
                }
                if (!Schema::hasColumn('rbr_rules', 'confidence_weight')) {
                    $table->decimal('confidence_weight', 4, 2)->default(0.85)->after('then_recommendation');
                }
                if (!Schema::hasColumn('rbr_rules', 'description')) {
                    $table->text('description')->nullable()->after('confidence_weight');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe fallback
    }
};

