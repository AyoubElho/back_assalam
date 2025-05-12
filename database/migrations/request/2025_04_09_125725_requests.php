<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('application_type', ['يتيم_أرملة', 'أسرة_معوزة']);
            $table->enum('status', [
                'قيد_المراجعة',         // pending (initial state)
                'قيد_التأكيدقيد_التأكيد',
                'غير_مكتمل',            // incomplete (one or more files invalid)
                'قيد_مراجعة_المسؤول',   // under review by admin
                'مقبول',                // validated
                'مرفوض',                // rejected (optional: if admin rejects whole request)
                'تمت_إعادة_رفع_الملفات', // files re-uploaded
                'قيد_الانتظار'          // added status "قيد_الانتظار"
            ])->default('قيد_المراجعة');
            $table->date('submission_date');
            $table->date('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
