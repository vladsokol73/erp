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
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id();
            $table->string('player_id', 255);
            $table->string('status', 16);
            $table->string('c2d_channel_id', 255);
            $table->bigInteger('tg_id');
            $table->string('prod_id', 255);
            $table->decimal('dep_sum', 10, 2)->nullable();
            $table->bigInteger('operator_id');

            $table->foreign('operator_id')
                ->references('operator_id')
                ->on('operators')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_logs');
    }
};
