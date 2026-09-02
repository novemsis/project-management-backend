<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->text('definition')->nullable();

            $table->boolean('smarter_spezifisch');
            $table->boolean('smarter_messbar');
            $table->boolean('smarter_ambitioniert');
            $table->boolean('smarter_realistisch');
            $table->boolean('smarter_terminiert');
            $table->boolean('smarter_emotionalisiert');
            $table->boolean('smarter_ressourceneinsatz');

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
