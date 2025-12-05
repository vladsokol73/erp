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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('source', 100);
            $table->bigInteger('source_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('value')->default(0)->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('likes', function($table)
        {
            $table->dropForeign('likes_user_id_foreign');
            $table->dropIndex('likes_user_id_index');
        });
        Schema::dropIfExists('likes');
    }
};
