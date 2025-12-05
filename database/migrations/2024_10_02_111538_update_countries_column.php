<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function(Blueprint $table) {
            $table->renameColumn('country', 'name');
            $table->string('img')->nullable()->change();
        });
    }


    public function down(): void
    {
        Schema::table('countries', function(Blueprint $table) {
            $table->renameColumn('name', 'country');
            $table->string('img')->nullable(false)->change();
        });
    }
};
