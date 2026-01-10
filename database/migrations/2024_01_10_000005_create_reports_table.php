<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            
            $table->enum('type', ['weekly', 'anomaly', 'custom'])->default('weekly');
            $table->string('subject');
            $table->text('content');
            $table->json('data')->nullable(); // Stores chart data, metrics, etc.
            
            // Report queue status
            $table->enum('status', ['pending', 'approved', 'rejected', 'sent'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            // Recipients
            $table->json('recipients')->nullable();
            $table->timestamp('sent_at')->nullable();
            
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
