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
        Schema::dropIfExists('vacant_positions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('vacant_positions', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('position');
            $table->double('fraction');
            $table->integer('salary');
            $table->string('notice');
            $table->timestamps();
        });
    }
};
