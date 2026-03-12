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
        Schema::create('game_tag_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')
                ->constrained('games')
                ->cascadeOnDelete();
            $table->foreignId('tag_id')
                ->constrained('game_tags')
                ->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate tag assignment.
            $table->unique(['game_id', 'tag_id']);
            $table->index(['game_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_tag_map');
    }
};
