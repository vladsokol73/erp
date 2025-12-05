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
        Schema::create('operator_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('operator_id');
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('channel_id')->nullable();
            $table->boolean('is_new_client');
            $table->string('event_type', 20);
            $table->dateTime('event_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_logs');
    }
};
