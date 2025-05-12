<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('husband_tempos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cin')->unique();
            $table->string('phone');
            $table->date('birth_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('husband_tempos');
    }
};
