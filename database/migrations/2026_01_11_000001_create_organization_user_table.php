<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
        });

        DB::table('users')
            ->whereNotNull('organization_id')
            ->select(['id', 'organization_id', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                $rows = collect($users)->map(function ($user) {
                    return [
                        'organization_id' => $user->organization_id,
                        'user_id' => $user->id,
                        'created_at' => $user->created_at ?? now(),
                        'updated_at' => $user->updated_at ?? now(),
                    ];
                })->all();

                if (!empty($rows)) {
                    DB::table('organization_user')->upsert(
                        $rows,
                        ['organization_id', 'user_id'],
                        ['updated_at']
                    );
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
