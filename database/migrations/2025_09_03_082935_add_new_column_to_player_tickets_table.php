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
        Schema::table('player_tickets', function (Blueprint $table) {
            $table->boolean('is_valid_tg_id')->default(false);
            $table->boolean('is_valid_sum')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_tickets', function (Blueprint $table) {
            $table->dropColumn('is_valid_tg_id');
            $table->dropColumn('is_valid_sum');
        });
    }
};
