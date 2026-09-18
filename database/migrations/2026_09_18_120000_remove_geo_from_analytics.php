<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->dropColumn(['country', 'city']);
        });

        Schema::dropIfExists('analytics_geo_cache');
    }

    public function down(): void
    {
        Schema::table('analytics_sessions', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->after('ip');
            $table->string('city')->nullable()->after('country');
        });

        Schema::create('analytics_geo_cache', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64)->unique();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('cached_at');
            $table->index('cached_at');
        });
    }
};
