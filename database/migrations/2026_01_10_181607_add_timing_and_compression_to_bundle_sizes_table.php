<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bundle_sizes', function (Blueprint $table) {
            // Compression ratio (percentage saved by gzip/brotli)
            $table->unsignedTinyInteger('compression_ratio')->nullable()->after('load_time');
            
            // Download times by resource type (in ms)
            $table->unsignedInteger('javascript_download_time')->nullable()->after('javascript_requests');
            $table->unsignedInteger('css_download_time')->nullable()->after('css_requests');
            $table->unsignedInteger('image_download_time')->nullable()->after('image_requests');
            $table->unsignedInteger('font_download_time')->nullable()->after('font_requests');
            $table->unsignedInteger('html_download_time')->nullable()->after('html_transfer_size');
            
            // Total download time and slow request count
            $table->unsignedInteger('total_download_time')->nullable()->after('load_time');
            $table->unsignedInteger('slow_request_count')->nullable()->after('total_download_time');
        });
    }

    public function down(): void
    {
        Schema::table('bundle_sizes', function (Blueprint $table) {
            $table->dropColumn([
                'compression_ratio',
                'javascript_download_time',
                'css_download_time',
                'image_download_time',
                'font_download_time',
                'html_download_time',
                'total_download_time',
                'slow_request_count',
            ]);
        });
    }
};
