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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('preview_text')->nullable();
            $table->text('content');
            $table->string('status');
            $table->text('authors');
            $table->text('images')->nullable();
            $table->string('preview')->nullable();
            $table->text('search_data')->nullable();
            $table->text('reading_time')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();;
            $table->dateTime('publish_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors_posts', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::dropIfExists('posts');
    }
};
