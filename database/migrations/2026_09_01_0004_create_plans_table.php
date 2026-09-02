<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->text('strategic_definition')->nullable();
            $table->text('tactical_definition')->nullable();
            $table->text('operational_definition')->nullable();

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
