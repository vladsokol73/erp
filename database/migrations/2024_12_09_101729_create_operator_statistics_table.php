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
        Schema::create('operator_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('entity_id'); // ID канала или оператора
            $table->string('entity_type'); // Тип (например, 'channel' или 'operator')
            $this->extracted($table);
        });

        Schema::create('operator_channel_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('channel_id');
            $this->extracted($table);
        });

        Schema::create('overall_operator_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('total_new_client_chats');
            $table->integer('total_clients');
            $table->integer('total_inbox_messages');
            $table->integer('total_outbox_messages');
            $table->integer('total_time');
            $table->integer('total_reg_count');
            $table->integer('total_dep_count');
            $table->json('top_operators');
            $table->json('top_channels');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_statistics');
        Schema::dropIfExists('overall_operator_statistics');
    }

    /**
     * @param Blueprint $table
     * @return void
     */
    public function extracted(Blueprint $table): void
    {
        $table->integer('new_client_chats');
        $table->integer('total_clients');
        $table->integer('inbox_messages');
        $table->integer('outbox_messages');
        $table->dateTime('start_time')->nullable();
        $table->dateTime('end_time')->nullable();
        $table->integer('total_time');
        $table->integer('reg_count');
        $table->integer('dep_count');
        $table->timestamps();
    }
};
