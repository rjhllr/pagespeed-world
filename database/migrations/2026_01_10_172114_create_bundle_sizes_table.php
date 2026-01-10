<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            
            // Total sizes (in bytes)
            $table->unsignedBigInteger('total_size')->nullable();
            $table->unsignedBigInteger('total_transfer_size')->nullable();
            
            // Breakdown by type (in bytes)
            $table->unsignedBigInteger('javascript_size')->nullable();
            $table->unsignedBigInteger('javascript_transfer_size')->nullable();
            $table->unsignedBigInteger('css_size')->nullable();
            $table->unsignedBigInteger('css_transfer_size')->nullable();
            $table->unsignedBigInteger('image_size')->nullable();
            $table->unsignedBigInteger('image_transfer_size')->nullable();
            $table->unsignedBigInteger('font_size')->nullable();
            $table->unsignedBigInteger('font_transfer_size')->nullable();
            $table->unsignedBigInteger('html_size')->nullable();
            $table->unsignedBigInteger('html_transfer_size')->nullable();
            $table->unsignedBigInteger('other_size')->nullable();
            $table->unsignedBigInteger('other_transfer_size')->nullable();
            
            // Request counts
            $table->unsignedInteger('total_requests')->nullable();
            $table->unsignedInteger('javascript_requests')->nullable();
            $table->unsignedInteger('css_requests')->nullable();
            $table->unsignedInteger('image_requests')->nullable();
            $table->unsignedInteger('font_requests')->nullable();
            
            // Timing metrics
            $table->unsignedInteger('dom_content_loaded')->nullable(); // ms
            $table->unsignedInteger('load_time')->nullable(); // ms
            
            // Raw data
            $table->json('raw_data')->nullable();
            
            // Status
            $table->enum('status', ['success', 'error', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            
            $table->timestamps();

            $table->index(['page_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_sizes');
    }
};
