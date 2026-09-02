<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decisions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->boolean('carry_through');
            $table->text('description')->nullable();

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
