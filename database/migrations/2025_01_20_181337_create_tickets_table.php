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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // уникальный номер тикета
            $table->foreignId('topic_id')->constrained('ticket_topics');
            $table->foreignId('user_id')->constrained('users'); // кто создал тикет
            $table->string('status')->default('new'); // статус тикета
            $table->enum('priority', ['low', 'middle', 'high'])->default('low');
            $table->string('result', 255)->nullable();
            $table->timestamp('approved_at')->nullable(); // время согласования
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // кто сделал изменение
            $table->string('action'); // тип действия (создание, изменение статуса, добавление комментария и т.д.)
            $table->json('old_values')->nullable(); // старые значения
            $table->json('new_values')->nullable(); // новые значения
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_logs');
        Schema::dropIfExists('tickets');
    }
};
