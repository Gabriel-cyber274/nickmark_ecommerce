<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('state_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedInteger('amount'); // store in kobo/naira integer

            $table->timestamps();

            $table->unique(['state_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_fees');
    }
};
