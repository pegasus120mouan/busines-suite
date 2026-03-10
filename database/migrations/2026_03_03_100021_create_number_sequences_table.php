<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('type');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->integer('next_number')->default(1);
            $table->integer('padding')->default(5);
            $table->boolean('reset_yearly')->default(false);
            $table->integer('reset_year')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'type']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
