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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('slug')->nullable();
            $table->string('path');
            $table->boolean('is_registered')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->boolean('searchable')->default(true);
            $table->boolean('is_url')->default(false);
            $table->integer('code')->default(200);
            $table->unsignedBigInteger('sub_section_id')->nullable();
            $table->string('template')->nullable();
            $table->foreign('sub_section_id')->references('id')->on('sub_sections')->onDelete('set null');
            $table->text('search_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
