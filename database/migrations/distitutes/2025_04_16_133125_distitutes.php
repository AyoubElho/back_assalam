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
        Schema::create('distitutes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cin')->unique();
            $table->string('tel');
            $table->date('birth_date');
            $table->foreignId('husband_id')->constrained('husbands')->onDelete('cascade');
            $table->foreignId('created_by_admin')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distitutes');
    }
};
