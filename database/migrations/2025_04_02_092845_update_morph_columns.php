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
        // Добавление полиморфных связей для комментариев
        Schema::table('comments', function (Blueprint $table) {
            $table->morphs('commentable'); // Добавляет commentable_id и commentable_type

            $table->dropColumn('comment_for');
            $table->dropColumn('source_id');

            // Изменение поля user_id
            $table->unsignedBigInteger('user_id')->change();

            // Добавление внешнего ключа для user_id
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        // Добавление полиморфных связей для лайков
        Schema::table('likes', function (Blueprint $table) {
            $table->morphs('likeable'); // Добавляет likeable_id и likeable_type

            $table->dropColumn('source');
            $table->dropColumn('source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем внешний ключ и столбцы для комментариев
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Удаление внешнего ключа
            $table->dropColumn(['commentable_id', 'commentable_type']); // Удаление полей полиморфной связи
        });

        // Удаляем полиморфные поля для лайков
        Schema::table('likes', function (Blueprint $table) {
            $table->dropColumn(['likeable_id', 'likeable_type']); // Удаление полей полиморфной связи
        });
    }
};
