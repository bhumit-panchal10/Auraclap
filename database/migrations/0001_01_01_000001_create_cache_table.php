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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 255)->primary(); // Cache key
            $table->mediumText('value');          // Cache value
            $table->unsignedBigInteger('expiration'); // Expiration timestamp
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 255)->primary(); // Lock key
            $table->string('owner');              // Lock owner
            $table->unsignedBigInteger('expiration'); // Expiration timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
