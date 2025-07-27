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
        Schema::table('direction_studies', function (Blueprint $table) {
            $table->text('info')->nullable()->after('lvl_edu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direction_studies', function (Blueprint $table) {
            $table->dropColumn('info');
        });
    }
};
