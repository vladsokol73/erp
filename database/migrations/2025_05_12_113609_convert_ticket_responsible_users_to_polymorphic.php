<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Добавляем временные поля для morph-связи
        Schema::table('ticket_responsible_users', function (Blueprint $table) {
            $table->string('new_responsible_type')->nullable()->after('source_id');
            $table->unsignedBigInteger('responsible_id')->nullable()->after('new_responsible_type');
            $table->index(['new_responsible_type', 'responsible_id'], 'responsible_morph_index');
        });

        // 2. Обновляем данные по типу
        DB::table('ticket_responsible_users')->where('responsible_type', 'user')->update([
            'new_responsible_type' => 'App\\Models\\User\\User',
            'responsible_id' => DB::raw('value::bigint')
        ]);

        DB::table('ticket_responsible_users')->where('responsible_type', 'role')->update([
            'new_responsible_type' => 'App\\Models\\User\\Role',
            'responsible_id' => DB::raw('value::bigint')
        ]);

        DB::table('ticket_responsible_users')->where('responsible_type', 'permission')->update([
            'new_responsible_type' => 'App\\Models\\User\\Permission',
            'responsible_id' => DB::raw('value::bigint')
        ]);

        // 3. Удаляем старые поля
        Schema::table('ticket_responsible_users', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->dropColumn('responsible_type');
        });

        // 4. Переименовываем временное поле обратно
        Schema::table('ticket_responsible_users', function (Blueprint $table) {
            $table->renameColumn('new_responsible_type', 'responsible_type');
        });
    }

    public function down(): void
    {
        // 1. Добавляем старые поля обратно
        Schema::table('ticket_responsible_users', function (Blueprint $table) {
            $table->enum('responsible_type', ['user', 'role', 'permission'])->nullable()->after('source_id');
            $table->string('value')->nullable()->after('responsible_type');
        });

        // 2. Восстанавливаем данные из morph-связи
        DB::table('ticket_responsible_users')->where('responsible_type', 'App\\Models\\User\\User')->update([
            'responsible_type' => 'user',
            'value'            => DB::raw('CAST(`responsible_id` AS CHAR)'),
        ]);

        DB::table('ticket_responsible_users')->where('responsible_type', 'App\\Models\\User\\Role')->update([
            'responsible_type' => 'role',
            'value'            => DB::raw('CAST(`responsible_id` AS CHAR)'),
        ]);

        DB::table('ticket_responsible_users')->where('responsible_type', 'App\\Models\\User\\Permission')->update([
            'responsible_type' => 'permission',
            'value'            => DB::raw('CAST(`responsible_id` AS CHAR)'),
        ]);

        // 3. Удаляем morph-поля
        Schema::table('ticket_responsible_users', function (Blueprint $table) {
            $table->dropIndex('responsible_morph_index');
            $table->dropColumn('responsible_type');
            $table->dropColumn('responsible_id');
        });
    }
};
