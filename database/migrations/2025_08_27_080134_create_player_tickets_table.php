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
        Schema::create('player_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // уникальный номер тикета
            $table->foreignId('user_id')->constrained('users'); // кто создал тикет
            $table->string('status')->default('On Approve'); // статус тикета
            $table->integer('player_id');
            $table->string('type', 8);
            $table->integer('tg_id');
            $table->string('screen_url');
            $table->decimal('sum', 10, 2);
            $table->timestamp('approved_at')->nullable(); // время согласования
            $table->string('result', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('operator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_tickets');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('operator_id');
        });
    }
};
