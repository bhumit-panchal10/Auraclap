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
        Schema::table('cache', function (Blueprint $table) {
            $table->string('key', 255)->change(); // Ensure `key` is a string
            $table->mediumText('value')->change(); // Ensure `value` can hold larger data
            $table->unsignedBigInteger('expiration')->change(); // Allow larger timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cache', function (Blueprint $table) {
            $table->integer('key')->change(); // Revert if necessary
            $table->text('value')->change();
            $table->integer('expiration')->change();
        });
    }
};
