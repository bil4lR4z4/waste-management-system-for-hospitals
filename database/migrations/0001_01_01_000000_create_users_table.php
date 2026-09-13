<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('plan_password');
            $table->text('city_ids')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('ntn')->nullable();
            $table->string('pra')->nullable();
            $table->string('secp')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_qr_code')->nullable();
            $table->string('bank_account_holder')->nullable();

            $table->string('phone_account_name')->nullable();
            $table->string('phone_account_no')->nullable();
            $table->string('phone_qr_code')->nullable();
            $table->string('phone_account_holder')->nullable();

            $table->rememberToken();
            $table->string('role')->default('Manager')->comment('Admin, Manager, Employee');
            $table->integer('parent_id')->nullable();
            $table->tinyInteger('trash')->default(0)->comment('0=not deleted, 1=deleted');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        DB::table('users')->insert([
            'first_name' => 'Muhammad',
            'last_name'  => 'Bilal Soomro',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('admin@gmail.com'),
            'role'       => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
