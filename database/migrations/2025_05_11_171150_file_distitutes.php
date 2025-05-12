<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('file_distitutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distitute_id')->constrained('distitutes')->onDelete('cascade'); // Foreign key to distitutes
            $table->string('file_type');
            $table->string('file_path');
            $table->string('status');
            $table->text('note_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_distitutes');
    }
};
