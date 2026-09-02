<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performed_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignUuid('target_id')
                ->nullable()
                ->constrained('targets')
                ->cascadeOnDelete();

            $table->foreignUuid('plan_id')
                ->nullable()
                ->constrained('plans')
                ->cascadeOnDelete();

            $table->foreignUuid('decision_id')
                ->nullable()
                ->constrained('decisions')
                ->cascadeOnDelete();

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performed_checks');
    }
};
