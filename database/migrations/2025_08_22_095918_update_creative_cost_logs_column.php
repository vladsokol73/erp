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
            $table->string('creative_code', 16)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creative_cost_logs', function (Blueprint $table) {
            $table->bigInteger('creative_code')->change();
        });
    }
};
