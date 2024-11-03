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
        Schema::table('academic_journals', function (Blueprint $table) {
            $table->text('search_data')->nullable()->after('for_authors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_journals', function (Blueprint $table) {
            $table->dropColumn('search_data');
        });
    }
};
