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
        Schema::create('ai_retention_reports_test', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->integer('operator_id');
            $table->integer('user_id');
            $table->smallInteger('score');
            $table->text('comment');
            $table->text('analysis');
            $table->json('raw_payload');
            $table->date('conversation_date');
            $table->text('prompt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_retention_reports_test');
    }
};
