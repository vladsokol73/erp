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
        // Обновление responsible_type в ticket_responsible_users
        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\User')
            ->update(['responsible_type' => 'App\\Models\\User\\User']);

        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\Role')
            ->update(['responsible_type' => 'App\\Models\\User\\Role']);

        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\Permission')
            ->update(['responsible_type' => 'App\\Models\\User\\Permission']);

        // Обновление notifiable_type в notifications
        DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->update(['notifiable_type' => 'App\\Models\\User\\User']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откат для ticket_responsible_users
        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\User\\User')
            ->update(['responsible_type' => 'App\\Models\\User']);

        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\User\\Role')
            ->update(['responsible_type' => 'App\\Models\\Role']);

        DB::table('ticket_responsible_users')
            ->where('responsible_type', 'App\\Models\\User\\Permission')
            ->update(['responsible_type' => 'App\\Models\\Permission']);

        // Откат для notifications
        DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User\\User')
            ->update(['notifiable_type' => 'App\\Models\\User']);
    }
};
