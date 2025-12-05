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
        Schema::create('ticket_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_topic_id')->constrained('ticket_topics')->onDelete('cascade');
            $table->string('name'); // название поля
            $table->string('label'); // метка поля для отображения
            $table->enum('type', ['text', 'number', 'select', 'textarea', 'date', 'file', 'checkbox']);
            $table->json('validation_rules')->nullable(); // правила валидации
            $table->json('options')->nullable(); // дополнительные опции (для select - варианты выбора, для text - макс длина и т.д.)
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_form_fields');
    }
};
