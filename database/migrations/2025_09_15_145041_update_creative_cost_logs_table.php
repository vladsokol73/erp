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
        Schema::table('creative_cost_logs', function (Blueprint $table) {
           $table->text('sub2')->nullable();
            $table->text('sub4')->nullable();
            $table->text('sub5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creative_cost_logs', function (Blueprint $table) {
            $table->dropColumn('sub2');
            $table->dropColumn('sub4');
            $table->dropColumn('sub5');
        });
    }
};
