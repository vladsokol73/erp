<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->string('sub1', 255)->nullable();
            $table->string('sub2', 255)->nullable();
            $table->string('sub3', 255)->nullable();
            $table->string('sub4', 255)->nullable();
            $table->string('sub5', 255)->nullable();
            $table->string('sub6', 255)->nullable();
            $table->string('sub7', 255)->nullable();
            $table->string('sub8', 255)->nullable();
            $table->string('sub9', 255)->nullable();
            $table->string('sub10', 255)->nullable();
            $table->string('sub11', 255)->nullable();
            $table->string('sub12', 255)->nullable();
            $table->string('sub13', 255)->nullable();
            $table->string('sub14', 255)->nullable();
            $table->string('sub15', 255)->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('clients', function(Blueprint $table) {
            $table->dropColumn('sub1', 255);
            $table->dropColumn('sub2', 255);
            $table->dropColumn('sub3', 255);
            $table->dropColumn('sub4', 255);
            $table->dropColumn('sub5', 255);
            $table->dropColumn('sub6', 255);
            $table->dropColumn('sub7', 255);
            $table->dropColumn('sub8', 255);
            $table->dropColumn('sub9', 255);
            $table->dropColumn('sub10', 255);
            $table->dropColumn('sub11', 255);
            $table->dropColumn('sub12', 255);
            $table->dropColumn('sub13', 255);
            $table->dropColumn('sub14', 255);
            $table->dropColumn('sub15', 255);
        });
    }
};
