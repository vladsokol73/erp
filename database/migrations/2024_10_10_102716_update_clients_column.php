<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Преобразование pb_last_mssg в timestamp
        DB::statement('ALTER TABLE clients ALTER COLUMN pb_last_mssg TYPE timestamp(0) without time zone USING pb_last_mssg::timestamp(0) without time zone');

        // Преобразование pb_channelsub в smallint
        DB::statement('ALTER TABLE clients ALTER COLUMN pb_channelsub TYPE smallint USING pb_channelsub::smallint');

        Schema::table('clients', function (Blueprint $table) {
            $table->dateTime('pb_last_mssg')->nullable()->change();
            $table->smallInteger('pb_channelsub')->nullable(false)->default(0)->change();
        });
    }


    public function down(): void
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->text('pb_last_mssg')->nullable()->change();
            $table->string('pb_channelsub')->nullable()->change();
        });
    }
};
