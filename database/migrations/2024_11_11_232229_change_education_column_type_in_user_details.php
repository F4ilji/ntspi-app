<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->text('education')->change(); // Изменяем тип на text
        });
    }

    public function down() : void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('education')->change(); // Возвращаем тип обратно на string
        });
    }
};
