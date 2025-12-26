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
        Schema::create('command', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->string('description');
            $table->text('response')->nullable();
            $table->enum('type', ['list','text'])->default('text');
            $table->string('target_table')->nullable();
            $table->string('target_column')->nullable();
            $table->json('fields')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('command');
    }
};
