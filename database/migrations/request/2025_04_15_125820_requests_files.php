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
        Schema::create('request_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('file_type', [
                'طلب_الترشيح',
                'البطاقة_الوطنية',
                'بطاقة_الرميد',
                'الحالة_المدنية',
                'عقد_الازدياد',
                'شهادة_الوفاة',
                'شهادة_الحياة',
                'شهادة_حسن_السيرة',
                'شهادة_طبية',
                'عقد_الزواج',
                'شهادة_عدم_الزواج',
                'صورة_شخصية',
                'صورة_عائلية'
            ]);
            $table->string("file_path");
            $table->text("note_admin")->nullable(); // Admin note field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_files');
    }
};
