<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->date('week_start')
                ->nullable()
                ->after('user_id');

            $table->index([
                'user_id',
                'week_start',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->dropIndex([
                'shopping_lists_user_id_week_start_index'
            ]);

            $table->dropColumn('week_start');
        });
    }
};