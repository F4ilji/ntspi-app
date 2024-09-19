<?php

use App\Models\Department;
use App\Models\EducationalProgram;
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
        Schema::create('program_department', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EducationalProgram::class);
            $table->foreignIdFor(Department::class);
            $table->integer('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_department');
    }
};
