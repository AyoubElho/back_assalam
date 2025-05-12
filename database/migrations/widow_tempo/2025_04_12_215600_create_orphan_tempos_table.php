<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orphan_tempos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widow_id')->constrained('widow_tempos');
            $table->string('full_name');
            $table->boolean('is_studying');
            $table->date('birth_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphan_tempos');
    }
};
