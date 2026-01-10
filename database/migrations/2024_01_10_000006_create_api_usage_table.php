<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Track PSI API usage to respect rate limits
        Schema::create('api_usage', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('requests_count')->default(0);
            $table->integer('daily_quota')->default(25000);
            $table->timestamps();

            $table->unique('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage');
    }
};
