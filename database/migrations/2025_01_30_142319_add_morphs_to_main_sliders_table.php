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
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->unsignedBigInteger('slidable_id')->nullable(); // Поле для идентификатора
            $table->string('slidable_type')->nullable(); // Поле для типа модели
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->dropColumn(['slidable_id', 'slidable_type']);
        });
    }
};
