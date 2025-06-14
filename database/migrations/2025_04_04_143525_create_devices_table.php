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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('main_feed_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_device_id')->nullable()->constrained('devices')->onDelete('set null');
            $table->string('device_type');
            $table->string('manufacturer');
            $table->string('device_model');
            $table->string('device_status');
            $table->boolean('parent_device')->default(true);
            $table->json('parameters');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
