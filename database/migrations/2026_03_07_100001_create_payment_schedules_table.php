<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
