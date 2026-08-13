<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->unsignedInteger('sms_sent_count')->default(0)->after('whatsapp');
            $table->timestamp('last_sms_sent_at')->nullable()->after('sms_sent_count');
        });
    }

    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn(['sms_sent_count', 'last_sms_sent_at']);
        });
    }
};
