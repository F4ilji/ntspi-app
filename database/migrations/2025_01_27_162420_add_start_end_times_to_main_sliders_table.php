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
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->after('is_active'); // Дата и время начала действия слайда
            $table->timestamp('end_time')->nullable()->after('start_time'); // Дата и время окончания действия слайда
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']); // Удаление колонок при откате миграции
        });
    }
};
