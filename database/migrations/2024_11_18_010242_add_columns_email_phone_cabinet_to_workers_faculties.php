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
        Schema::table('workers_faculties', function (Blueprint $table) {
            $table->string('service_email')->nullable()->after('position');
            $table->string('service_phone')->nullable()->after('service_email');
            $table->string('cabinet')->nullable()->after('service_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers_faculties', function (Blueprint $table) {
            $table->dropColumn('service_email');
            $table->dropColumn('service_phone');
            $table->dropColumn('cabinet');
        });
    }
};
