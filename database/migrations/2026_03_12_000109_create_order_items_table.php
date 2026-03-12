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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('game_id')
                ->constrained('games')
                ->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->char('currency', 3);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            // Indexes for lookups.
            $table->index('order_id');
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
