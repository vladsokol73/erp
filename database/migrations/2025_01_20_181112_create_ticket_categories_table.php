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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('slug')->unique();
            $table->text('description', 256)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_responsible_users', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['topic', 'topic_approval', 'ticket']);
            $table->unsignedBigInteger('source_id');
            $table->enum('responsible_type', ['user', 'role', 'permission'])->default('user');
            $table->string('value', 255); // ответственные за обработку
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_responsible_users');
        Schema::dropIfExists('ticket_categories');
    }
};
