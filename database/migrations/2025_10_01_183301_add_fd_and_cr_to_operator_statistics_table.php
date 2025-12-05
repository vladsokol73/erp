<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operator_statistics', function (Blueprint $table) {
            $table->integer('fd')->default(0);
            $table->decimal('cr_dialog_to_fd', 5, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('operator_statistics', function (Blueprint $table) {
            $table->dropColumn('fd');
            $table->dropColumn('cr_dialog_to_fd');
        });
    }
};
