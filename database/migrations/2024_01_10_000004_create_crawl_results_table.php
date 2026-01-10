<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->enum('strategy', ['mobile', 'desktop']);
            
            // Core Web Vitals
            $table->float('performance_score')->nullable();
            $table->float('accessibility_score')->nullable();
            $table->float('best_practices_score')->nullable();
            $table->float('seo_score')->nullable();
            
            // Timing metrics (in milliseconds)
            $table->float('first_contentful_paint')->nullable();
            $table->float('largest_contentful_paint')->nullable();
            $table->float('total_blocking_time')->nullable();
            $table->float('cumulative_layout_shift')->nullable();
            $table->float('speed_index')->nullable();
            $table->float('time_to_interactive')->nullable();
            
            // Raw API response
            $table->json('raw_response')->nullable();
            
            // Status
            $table->enum('status', ['success', 'error', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            
            $table->timestamps();

            $table->index(['page_id', 'strategy', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_results');
    }
};
