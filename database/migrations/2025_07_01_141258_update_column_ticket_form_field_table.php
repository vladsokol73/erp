<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Удалить старый CHECK, если он есть
        DB::statement('ALTER TABLE ticket_form_fields DROP CONSTRAINT IF EXISTS ticket_form_fields_type_check');

        // Добавить новый список значений
        DB::statement("ALTER TABLE ticket_form_fields ADD CONSTRAINT ticket_form_fields_type_check CHECK (
            type IN (
                'text', 'number', 'select', 'multiselect', 'country', 'project', 'textarea', 'date', 'file', 'checkbox'
            )
        )");
    }

    public function down(): void
    {
        // Откат: убираем 'project'
        DB::statement('ALTER TABLE ticket_form_fields DROP CONSTRAINT IF EXISTS ticket_form_fields_type_check');

        DB::statement("ALTER TABLE ticket_form_fields ADD CONSTRAINT ticket_form_fields_type_check CHECK (
            type IN (
                'text', 'number', 'select', 'multiselect', 'country', 'textarea', 'date', 'file', 'checkbox'
            )
        )");
    }
};
