<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('to_dos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->text('description');

            $table->boolean('done')->default(false);

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('to_dos');
    }
};
