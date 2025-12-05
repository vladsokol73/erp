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
        Schema::table('ticket_field_values', function (Blueprint $table) {
            $table->dropForeign(['field_id']); // Удаляем старый ключ
            $table->foreign('field_id')
                ->references('id')
                ->on('ticket_form_fields')
                ->onDelete('cascade'); // Добавляем каскадное удаление
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_field_values', function (Blueprint $table) {
            $table->dropForeign(['field_id']);
            $table->foreign('field_id')
                ->references('id')
                ->on('ticket_form_fields');
        });
    }

};
