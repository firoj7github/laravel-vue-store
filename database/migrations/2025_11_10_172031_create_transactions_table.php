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
        Schema::create('transactions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('item_id')       // foreign key to items table
              ->constrained('products')
              ->onDelete('cascade');
        $table->unsignedBigInteger('lot_id');
        $table->integer('quantity');
        $table->decimal('price', 10, 2);
        $table->decimal('total_price', 10, 2);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
