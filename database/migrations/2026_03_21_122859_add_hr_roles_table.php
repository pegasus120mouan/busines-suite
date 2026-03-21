<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des rôles RH
        Schema::create('hr_roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->integer('level')->default(0);
            $table->boolean('is_manager')->default(false);
            $table->boolean('can_approve_leaves')->default(false);
            $table->boolean('can_manage_team')->default(false);
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        // Ajouter role_id aux employés
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('hr_role_id')->nullable()->after('job_position_id');
            $table->foreign('hr_role_id')->references('id')->on('hr_roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['hr_role_id']);
            $table->dropColumn('hr_role_id');
        });
        Schema::dropIfExists('hr_roles');
    }
};
