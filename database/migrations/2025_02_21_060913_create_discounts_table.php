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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->integer('max_uses')->nullable(); // Max times a discount can be used
            $table->integer('per_user_limit')->nullable(); // Max times per user
            $table->boolean('first_time_only')->default(false); // First-time user discount
            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_to')->nullable();
            $table->decimal('min_cart_total', 10, 2)->nullable();
            $table->json('applicable_products')->nullable(); // Store product IDs as JSON
            $table->json('applicable_categories')->nullable(); // Store category IDs as JSON
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
