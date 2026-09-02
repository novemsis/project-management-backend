<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performed_checks_to_dos', function (Blueprint $table) {
            $table->foreignUuid('performed_check_id')
                ->constrained('performed_checks')
                ->cascadeOnDelete();

            $table->foreignUuid('to_do_id')
                ->constrained('to_dos')
                ->cascadeOnDelete();

            $table->primary([
                'performed_check_id',
                'to_do_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performed_checks_to_dos');
    }
};
