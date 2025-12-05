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
        Schema::table('product_logs', function (Blueprint $table) {
            $table->string('player_id', 255)->nullable()->change();
            $table->string('status', 16)->nullable()->change();
            $table->string('c2d_channel_id', 255)->nullable()->change();
            $table->bigInteger('tg_id')->nullable()->change();
            $table->string('prod_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_logs', function (Blueprint $table) {
            $table->string('player_id', 255)->change();
            $table->string('status', 16)->change();
            $table->string('c2d_channel_id', 255)->change();
            $table->bigInteger('tg_id')->change();
            $table->string('prod_id', 255)->change();
        });
    }
};
