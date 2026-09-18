<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 36)->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('entry_page');
            $table->string('ip', 45);
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->enum('device_type', ['desktop', 'tablet', 'mobile'])->nullable();
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['visitor_id', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
