<?php

use App\Models\User\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();

            $table->string('original_url', 2048);
            $table->string('short_code', 6);
            $table->string('domain', 255);

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::table('permissions')->insert(
            [
                'title' => 'Short URL',
                'guard_name' => 'shorter',
                'description' => 'Доступ к разделу Short URL.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
        DB::table('permissions')->where('guard_name', 'shorter')->delete();
    }
};
