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
        Schema::create('posts_tg_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // Поле post_id
            $table->unsignedBigInteger('tg_post_id'); // Поле vk_post_id
            $table->dateTime('unchange_time_after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts_tg_posts');
    }
};
