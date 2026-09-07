<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('username')->unique();
            $table->text('password');
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
