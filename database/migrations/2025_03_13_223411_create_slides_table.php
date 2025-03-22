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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('image')->nullable();
            $table->string('link');
            $table->text('settings')->nullable();
            $table->string('color_theme');
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_time')->nullable(); // Дата и время начала действия слайда
            $table->timestamp('end_time')->nullable(); // Дата и время окончания действия слайда
            $table->integer('sort')->nullable();
            $table->foreignId('slider_id')->references('id')->on('sliders')->onDelete('cascade');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
