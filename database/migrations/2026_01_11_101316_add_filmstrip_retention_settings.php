<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedInteger('max_filmstrip_retention')->default(10)->after('min_crawl_interval_hours');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedInteger('filmstrip_retention_count')->nullable()->after('next_crawl_at');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('max_filmstrip_retention');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('filmstrip_retention_count');
        });
    }
};
