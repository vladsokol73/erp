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
        // Создаём ENUM-тип в БД, если его нет
        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'ticket_field_type') THEN
                CREATE TYPE ticket_field_type AS ENUM ('text', 'number', 'select', 'multiselect', 'country', 'textarea', 'date', 'file', 'checkbox');
            END IF;
        END $$;");

        // Меняем тип колонки
        DB::statement('ALTER TABLE ticket_form_fields ALTER COLUMN type SET DATA TYPE ticket_field_type USING type::ticket_field_type');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE ticket_form_fields ALTER COLUMN type SET DATA TYPE VARCHAR(255)');

        // Удаляем ENUM-тип (если больше не нужен)
        DB::statement('DROP TYPE IF EXISTS ticket_field_type');
    }
};
