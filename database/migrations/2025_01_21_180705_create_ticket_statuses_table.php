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
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название статуса
            $table->string('slug');
            $table->string('color')->nullable(); // Цвет для отображения статуса
            $table->boolean('is_default')->default(false); // Является ли статус статусом по умолчанию
            $table->boolean('is_final')->default(false); // Является ли статус конечным (например, "Закрыт", "Отменен")
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_category_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('ticket_statuses')->onDelete('cascade');
            $table->boolean('is_default')->default(false); // Статус по умолчанию для данной категории
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Уникальный индекс для предотвращения дублирования статусов в категории
            $table->unique(['category_id', 'status_id']);
        });

        // Изменяем поле status в таблице tickets на foreign key
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreignId('status_id')->after('user_id')->constrained('ticket_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
            $table->string('status')->default('new')->after('user_id');
        });

        Schema::dropIfExists('ticket_category_statuses');
        Schema::dropIfExists('ticket_statuses');
    }
};
