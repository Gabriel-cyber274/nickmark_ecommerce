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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('phone_number1')->nullable()->after('account_name');
            $table->string('phone_number2')->nullable()->after('phone_number1');
            $table->string('phone_number3')->nullable()->after('phone_number2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['phone_number1', 'phone_number2', 'phone_number3']);
        });
    }
};
