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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('revenue', 15, 2)->default(0)->comment('Doanh thu (vé)');
            $table->decimal('tip', 15, 2)->default(0)->comment('Tiền tip');
            $table->decimal('penalty', 15, 2)->default(0)->comment('Tiền phạt');
            $table->decimal('facility', 15, 2)->default(0)->comment('Cơ sở vật chất');
            $table->text('note')->nullable();
            $table->date('recorded_at');
            $table->boolean('is_locked')->default(false)->comment('Đã chốt ngày');
            $table->timestamps();
            
            $table->index('recorded_at');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
