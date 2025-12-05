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
        Schema::table('ticket_topics', function (Blueprint $table) {
            // Важно: сначала нужно удалить ограничение внешнего ключа, чтобы изменить колонку
            $table->dropForeign(['approval_id']);

            // Меняем approval_id на nullable
            $table->foreignId('approval_id')->nullable()->change();

            // Восстанавливаем внешний ключ
            $table->foreign('approval_id')->references('id')->on('ticket_responsible_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_topics', function (Blueprint $table) {
            // Откатываем: удаляем внешний ключ
            $table->dropForeign(['approval_id']);

            // Делаем снова not nullable
            $table->foreignId('approval_id')->nullable(false)->change();

            // Восстанавливаем внешний ключ
            $table->foreign('approval_id')->references('id')->on('ticket_responsible_users')->cascadeOnDelete();
        });
    }
};
