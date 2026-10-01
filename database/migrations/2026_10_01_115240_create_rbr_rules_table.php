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
        Schema::create('rbr_rules', function (Blueprint $table) {
            $table->id('id_rule'); // Primary Key
            
            // Kolom untuk Knowledge Base IF-THEN
            $table->text('kondisi_if');
            $table->text('kesimpulan_then');
            $table->boolean('status_aktif')->default(1); // 1 = Aktif, 0 = Nonaktif
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbr_rules');
    }
};
