<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->enum('type', ['invoice', 'quote', 'reminder'])->default('invoice');
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->string('logo_position')->default('left');
            $table->string('color_primary')->default('#059669');
            $table->string('color_secondary')->default('#6B7280');
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_payment_info')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
