<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('directions', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->string('tenant_id', 36)->change();
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('directions', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->unsignedBigInteger('tenant_id')->change();
            $table->index('tenant_id');
        });
    }
};
