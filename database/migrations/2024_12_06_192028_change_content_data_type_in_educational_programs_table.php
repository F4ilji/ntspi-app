<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educational_programs', function (Blueprint $table) {
            $table->text('about_program')->change();
            $table->text('program_features')->change();

        });
    }

    public function down(): void
    {
        Schema::table('educational_programs', function (Blueprint $table) {
            $table->string('about_program')->change();
            $table->string('program_features')->change();
        });
    }

};
