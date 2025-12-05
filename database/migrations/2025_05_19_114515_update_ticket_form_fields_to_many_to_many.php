<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Удаляем внешнюю связь и колонку ticket_topic_id, если она есть
        Schema::table('ticket_form_fields', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_form_fields', 'ticket_topic_id')) {
                $table->dropForeign(['ticket_topic_id']);
                $table->dropColumn('ticket_topic_id');
            }

            if (Schema::hasColumn('ticket_form_fields', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });

        // Создаем таблицу связи many-to-many
        Schema::create('ticket_form_field_topic', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_topic_id')
                ->constrained('ticket_topics')
                ->onDelete('cascade');

            $table->foreignId('ticket_form_field_id')
                ->constrained('ticket_form_fields')
                ->onDelete('cascade');

            $table->integer('sort_order')->default(0); // новый порядок
            $table->timestamps();

            $table->unique(['ticket_topic_id', 'ticket_form_field_id'], 'topic_field_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_form_field_topic');

        Schema::table('ticket_form_fields', function (Blueprint $table) {
            // Восстанавливаем поля, если нужно откатить миграцию
            $table->foreignId('ticket_topic_id')
                ->nullable()
                ->constrained('ticket_topics')
                ->onDelete('cascade');

            $table->integer('sort_order')->default(0);
        });
    }
};
