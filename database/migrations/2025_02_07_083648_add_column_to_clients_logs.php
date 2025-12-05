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
        Schema::table('clients_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('c2d_client_id')->nullable()->after('client_id');
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients_logs', function (Blueprint $table) {
            $table->dropColumn('c2d_client_id');
            $table->unsignedBigInteger('client_id')->change();
        });
    }
};
