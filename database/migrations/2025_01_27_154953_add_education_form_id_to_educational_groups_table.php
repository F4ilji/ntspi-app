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
        Schema::table('educational_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('education_form_id')->nullable()->after('faculty_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::table('educational_groups', function (Blueprint $table) {
            $table->dropColumn('education_form_id');
        });
    }
};
