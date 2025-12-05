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
            // Удаляем внешний ключ
            $table->dropForeign(['approval_id']);

            // Удаляем поле approval_id
            $table->dropColumn('approval_id');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_topics', function (Blueprint $table) {
            $table->foreignId('approval_id')->constrained('ticket_responsible_users')->onDelete('cascade');
        });
    }
};
