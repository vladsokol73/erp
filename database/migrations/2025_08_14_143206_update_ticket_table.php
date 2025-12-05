<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Удаляем уникальное ограничение
        DB::statement('ALTER TABLE tickets DROP CONSTRAINT tickets_ticket_number_unique');

        // 2. Создаём уникальный partial index только для не удалённых
        DB::statement('CREATE UNIQUE INDEX tickets_ticket_number_unique ON tickets(ticket_number) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откат — удаляем partial index
        DB::statement('DROP INDEX tickets_ticket_number_unique');

        // Восстанавливаем уникальное ограничение
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_ticket_number_unique UNIQUE (ticket_number)');
    }
};
