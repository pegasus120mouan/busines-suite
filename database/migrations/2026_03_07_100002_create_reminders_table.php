<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('level')->default(1);
            $table->enum('type', ['email', 'sms', 'letter', 'phone', 'manual'])->default('email');
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->date('scheduled_date');
            $table->datetime('sent_at')->nullable();
            $table->text('subject')->nullable();
            $table->text('message')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
