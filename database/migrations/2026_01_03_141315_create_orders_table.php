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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('discount_id')
                ->nullable()
                ->constrained('discount_codes')
                ->cascadeOnDelete();


            $table->string('reference')->nullable();
            $table->string('name');

            $table->string('email');

            $table->string('phone');

            $table->foreignId('state_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('address');

            $table->string('postal_code');
            $table->string('payment_method')->nullable();

            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
