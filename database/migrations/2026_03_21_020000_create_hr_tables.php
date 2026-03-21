<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });

        // 2. Job Positions
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->integer('expected_employees')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        // 3. Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('employee_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->integer('children_count')->default(0);
            $table->string('nationality')->nullable();
            $table->string('id_number')->nullable();
            $table->string('social_security_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('job_position_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->date('departure_date')->nullable();
            $table->enum('departure_reason', ['resignation', 'termination', 'retirement', 'other'])->nullable();
            $table->string('work_email')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('work_location')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('job_position_id')->references('id')->on('job_positions')->nullOnDelete();
        });

        // Add self-referencing FKs for employees
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('coach_id')->references('id')->on('employees')->nullOnDelete();
        });

        // Add manager FK to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('departments')->nullOnDelete();
        });

        // 4. Contracts
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('reference')->nullable();
            $table->string('name');
            $table->enum('contract_type', ['cdi', 'cdd', 'internship', 'freelance', 'temporary'])->default('cdi');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('job_position_id')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('trial_end_date')->nullable();
            $table->decimal('wage', 15, 2);
            $table->enum('wage_type', ['monthly', 'hourly', 'daily', 'yearly'])->default('monthly');
            $table->string('currency', 3)->default('XOF');
            $table->decimal('hourly_cost', 10, 2)->nullable();
            $table->integer('hours_per_week')->default(40);
            $table->text('advantages')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'running', 'expired', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('job_position_id')->references('id')->on('job_positions')->nullOnDelete();
        });

        // 5. Leave Types
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->decimal('default_days', 5, 2)->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });

        // 6. Leaves
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('number_of_days', 5, 2);
            $table->enum('request_unit', ['day', 'half_day', 'hours'])->default('day');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'refused', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // 7. Attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('worked_hours', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'holiday', 'weekend'])->default('present');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'date']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        // 8. Payslips
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('name');
            $table->date('date_from');
            $table->date('date_to');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_allowances', 15, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('bonus_amount', 15, 2)->default(0);
            $table->json('lines')->nullable();
            $table->string('currency', 3)->default('XOF');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'paid', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('contracts');
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['parent_id']);
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['coach_id']);
        });
        Schema::dropIfExists('employees');
        Schema::dropIfExists('job_positions');
        Schema::dropIfExists('departments');
    }
};
