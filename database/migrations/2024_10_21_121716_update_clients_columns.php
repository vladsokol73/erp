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
        // Преобразование c2d_last_mssg в timestamp
        DB::statement('ALTER TABLE clients ALTER COLUMN c2d_last_mssg TYPE timestamp(0) without time zone USING c2d_last_mssg::timestamp(0) without time zone');

        Schema::table('clients', function (Blueprint $table) {
            $table->dateTime('c2d_last_mssg')->nullable()->change();
            $table->string('c2d_client_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->text('c2d_last_mssg')->nullable()->change();
            $table->dropColumn('c2d_client_id');
        });
    }
};
