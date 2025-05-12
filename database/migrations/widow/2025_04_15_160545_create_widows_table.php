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
        Schema::create('widows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tel');
            $table->string('cin')->unique();
            $table->boolean('is_supported')->default(true);
            $table->date('birth_date');
            $table->foreignId('created_by_admin')->nullable()->constrained('users')->cascadeOnDelete(); // Make it nullable and optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widows');
    }
};
