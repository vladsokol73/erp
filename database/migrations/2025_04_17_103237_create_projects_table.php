<?php

use App\Models\Project;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('description', 512)->nullable();
            $table->string('currency', 5)->nullable();
            $table->timestamps();
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->foreignIdFor(Project::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');

        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
    }
};
