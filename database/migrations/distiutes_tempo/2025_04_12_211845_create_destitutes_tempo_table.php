<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('destitutes_tempos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cin')->unique();
            $table->string('phone');
            $table->date('birth_date');
            $table->foreignId('husband_id')->constrained('husband_tempos')->onDelete('cascade');
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destitutes_tempo');
    }
};
