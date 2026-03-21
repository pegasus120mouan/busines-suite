<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('direction_id')->nullable()->after('tenant_id')->constrained('directions')->onDelete('set null');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            $table->string('bank_iban')->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['direction_id']);
            $table->dropColumn(['direction_id', 'emergency_contact_relation', 'bank_iban']);
        });
    }
};
