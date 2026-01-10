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
        Schema::table('crawl_results', function (Blueprint $table) {
            $table->index(['page_id', 'strategy', 'status', 'created_at']);
            $table->index(['strategy', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crawl_results', function (Blueprint $table) {
            $table->dropIndex(['page_id', 'strategy', 'status', 'created_at']);
            $table->dropIndex(['strategy', 'status']);
        });
    }
};
