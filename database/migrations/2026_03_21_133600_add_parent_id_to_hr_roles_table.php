<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_roles', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('tenant_id')->constrained('hr_roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hr_roles', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
