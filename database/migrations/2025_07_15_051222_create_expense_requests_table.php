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
        Schema::create('expense_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->text('description')->nullable(); // شرح هزینه
            $table->decimal('amount', 15, 2); // مبلغ
            $table->string('sheba', 26); // شماره شبا
            $table->string('attachment_path')->nullable(); // مسیر پیوست
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'paid'
            ])->default('pending'); // وضعیت
            $table->text('rejection_reason')->nullable(); // دلیل رد
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_requests');
    }
};
