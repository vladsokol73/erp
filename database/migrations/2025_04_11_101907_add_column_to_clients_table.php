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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('currency', 5)->nullable();
        });

        Schema::table('product_logs', function (Blueprint $table) {
            $table->string('currency', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('product_logs', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
