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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment_for', 30)->default('creative')->nullable(1);
            $table->bigInteger('source_id');
            $table->text('comment');
            $table->bigInteger('user_id');
            $table->timestamps();
        });

        DB::table('permissions')->insert(
            [
                'title' => 'Creatives comments to see',
                'guard_name' => 'creatives.comments',
                'description' => 'Позволять ли видеть все комментарии, либо только свои.',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
        DB::table('permissions')->where('guard_name', 'creatives.comments')->delete();
    }
};
