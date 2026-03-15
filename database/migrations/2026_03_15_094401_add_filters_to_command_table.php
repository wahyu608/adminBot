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
         Schema::table('command', function (Blueprint $table) {
            $table->json('filters')->nullable()->after('target_column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('command', function (Blueprint $table) {
            $table->dropColumn('filters');
        });
    }
};
