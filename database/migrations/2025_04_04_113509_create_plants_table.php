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
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('owner_email');
            $table->string('status');
            $table->decimal('capacity', 12, 2);
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->bigInteger('last_updated')->nullable(); // timestamp as INT
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
