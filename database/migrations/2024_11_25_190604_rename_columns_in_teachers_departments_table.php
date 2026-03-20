<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('teachers_departments', function (Blueprint $table) {
            $table->renameColumn('email', 'service_email');
            $table->renameColumn('phone', 'service_phone');
        });
    }

    /**
     * Откатить миграцию.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers_departments', function (Blueprint $table) {
            $table->renameColumn('service_email', 'email');
            $table->renameColumn('service_phone', 'phone');
        });
    }
};
