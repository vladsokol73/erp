<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('creative_statistic')
            ->whereNull('cost')
            ->update(['cost' => 0]);

        Schema::table('creative_statistic', function (Blueprint $table) {
            $table->integer('cost')->default(0)->change();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('fd_a')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creative_statistic', function (Blueprint $table) {
            $table->integer('cost')->nullable()->change();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('fd_a');
        });
    }
};
