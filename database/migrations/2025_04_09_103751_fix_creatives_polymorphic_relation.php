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
        Schema::table('favorites', function (Blueprint $table) {
            $table->string('favoriteable_type')->nullable()->after('source');
            $table->unsignedBigInteger('favoriteable_id')->nullable()->after('favoriteable_type');


            $table->index(['favoriteable_type', 'favoriteable_id']);
        });

        DB::statement("UPDATE favorites SET favoriteable_type = CONCAT('App\\\\Models\\\\', UPPER(SUBSTR(source, 1, 1)), SUBSTR(source, 2, LENGTH(source) - 1)), favoriteable_id = source_id");


        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            if (!Schema::hasColumn('favorites', 'source')) {
                $table->string('source')->nullable()->after('id');
            }

            if (!Schema::hasColumn('favorites', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source');
            }

            DB::statement("UPDATE favorites SET source = LOWER(REGEXP_REPLACE(favoriteable_type, 'App\\\\Models\\\\(.*)', '\\1')), source_id = favoriteable_id WHERE favoriteable_type IS NOT NULL");

            $table->dropIndex(['favoriteable_type', 'favoriteable_id']);
            $table->dropColumn('favoriteable_type');
            $table->dropColumn('favoriteable_id');
        });
    }
};
