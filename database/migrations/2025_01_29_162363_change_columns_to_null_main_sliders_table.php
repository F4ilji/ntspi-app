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
            $table->string('title')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->dropColumn('link_text');
            $table->text('settings')->nullable()->after('link');
            $table->text('image')->nullable()->change();
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
            $table->string('title')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
            $table->string('link_text')->nullable(false);
            $table->dropColumn('settings');
            $table->string('image')->nullable()->change();
        });
    }
};
