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
        Schema::create('vk_tokens', function (Blueprint $table) {
            $table->id(); // Автоинкрементный ID
            $table->string('user_id')->unique(); // ID пользователя VK
            $table->text('access_token'); // Access token
            $table->text('refresh_token'); // Refresh token
            $table->text('id_token')->nullable(); // ID token (если требуется)
            $table->string('state');
            $table->string('scope');
            $table->text('device_id');
            $table->timestamp('token_expire');
            $table->timestamps(); // Поля created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vk_tokens');
    }
};
