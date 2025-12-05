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
        // Список доступных флагов
        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // например: ai_report, inactive, priority
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Полиморфная таблица для связей
        Schema::create('flaggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flag_id')->constrained()->cascadeOnDelete();
            $table->morphs('flaggable'); // flaggable_type, flaggable_id
            $table->timestamps();

            $table->unique(['flag_id', 'flaggable_id', 'flaggable_type'], 'flaggables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flaggables');
        Schema::dropIfExists('flags');
    }
};
