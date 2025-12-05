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
        Schema::create('creative_cost_logs', function (Blueprint $table) {
            $table->id();
            $table->string('clickid', 255);
            $table->bigInteger('company_id')->unsigned();
            $table->double('cost');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('creative_code')->unsigned();
            $table->dateTime('date');
            $table->timestamps();
        });

        Schema::table('creative_statistic', function (Blueprint $table) {
            $table->integer('cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creative_cost_logs');
        Schema::table('creative_statistic', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};
