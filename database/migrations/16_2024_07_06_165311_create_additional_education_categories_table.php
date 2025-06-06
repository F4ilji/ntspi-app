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
        Schema::create('additional_education_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->boolean('is_active');
            $table->unsignedBigInteger('dir_addit_educat_id')->nullable();
            $table->foreign('dir_addit_educat_id')->references('id')->on('direction_additional_education');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_education_categories');
    }
};
