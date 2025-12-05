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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('clickid', 255)->nullable();
            $table->bigInteger('tg_id')->nullable();
            $table->string('source_id', 255)->nullable();
            $table->string('prod_id', 255)->nullable();
            $table->string('player_id', 255)->nullable();
            $table->tinyInteger('reg')->default(0);
            $table->tinyInteger('dep')->default(0);
            $table->tinyInteger('redep')->default(0);
            $table->dateTime('reg_date')->nullable();
            $table->dateTime('dep_date')->nullable();
            $table->dateTime('redep_date')->nullable();
            $table->decimal('dep_sum', 10, 2)->nullable();
            $table->tinyInteger('is_pb')->default(0);
            $table->dateTime('is_pb_date')->nullable();
            $table->string('pb_bot_name');
            $table->text('pb_last_mssg')->nullable();
            $table->string('pb_channelsub', 255)->nullable();
            $table->dateTime('pb_channelsub_date')->nullable();
            $table->tinyInteger('is_c2d')->default(0);
            $table->dateTime('is_c2d_date')->nullable();
            $table->string('c2d_channel_id', 255)->nullable();
            $table->text('c2d_tags')->nullable();
            $table->text('c2d_last_mssg')->nullable();
            $table->string('geo_click', 255)->nullable();
            $table->string('lang', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('oc', 50)->nullable();
            $table->string('ver_oc', 50)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('ip', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
