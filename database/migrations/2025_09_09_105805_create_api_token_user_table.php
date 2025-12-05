<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_token_user', function (Blueprint $table) {
            $table->unsignedBigInteger('api_token_id');
            $table->unsignedBigInteger('user_id');

            $table->primary(['api_token_id', 'user_id']);

            $table->foreign('api_token_id')
                ->references('id')
                ->on('api_tokens')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // migrate existing one-to-many data to pivot table
        DB::table('users')
            ->whereNotNull('api_token_id')
            ->orderBy('id')
            ->chunkById(1000, function ($users) {
                $rows = [];
                foreach ($users as $user) {
                    $rows[] = [
                        'api_token_id' => $user->api_token_id,
                        'user_id' => $user->id,
                    ];
                }
                if (!empty($rows)) {
                    DB::table('api_token_user')->insertOrIgnore($rows);
                }
            });

        // drop the legacy column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'api_token_id')) {
                $table->dropColumn('api_token_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // restore legacy column
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'api_token_id')) {
                $table->unsignedBigInteger('api_token_id')->nullable();
            }
        });

        // fill users.api_token_id with the first related token (if any)
        // This keeps backward compatibility when rolling back
        $records = DB::table('api_token_user')
            ->select('user_id', DB::raw('MIN(api_token_id) as api_token_id'))
            ->groupBy('user_id')
            ->get();

        foreach ($records as $record) {
            DB::table('users')
                ->where('id', $record->user_id)
                ->update(['api_token_id' => $record->api_token_id]);
        }

        Schema::dropIfExists('api_token_user');
    }
};
