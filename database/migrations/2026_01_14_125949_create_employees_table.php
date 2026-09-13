<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('employee_type')->nullable();
            $table->string('security_no')->nullable();
            $table->string('oldage_benefits')->nullable();
            $table->text('license_no')->nullable();
            $table->string('phone_no', 20)->nullable();
            $table->string('cnic_no', 20)->unique();
            $table->text('address')->nullable();
            $table->decimal('salary', 10, 2)->default(0);
            $table->decimal('allowance', 10, 2)->default(0);
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('employee_image')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->tinyInteger('trash')->default(0)->comment('0 = active, 1 = trash');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->text('reason')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('education')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
