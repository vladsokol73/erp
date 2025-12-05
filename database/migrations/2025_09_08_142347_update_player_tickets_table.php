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
            $table->bigInteger('player_id')->change();
            $table->bigInteger('tg_id')->change();
            $table->decimal('sum', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_tickets', function (Blueprint $table) {
            $table->integer('player_id')->change();
            $table->integer('tg_id')->change();
            $table->decimal('sum', 10, 2)->change();
        });
    }
};
