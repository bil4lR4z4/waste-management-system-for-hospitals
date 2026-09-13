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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('owner_name')->nullable();
            $table->string('email')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('ntn')->nullable();
            $table->string('phc')->nullable();
            $table->string('pra')->nullable();
            $table->string('secp')->nullable();

            $table->text('facilities')->nullable();

            $table->text('address')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('contact_number')->nullable();
            $table->decimal('payment', 8, 2)->nullable();

            $table->integer('number_of_beds')->default(0);

            $table->string('waste_generation_volume')->nullable();
            $table->string('waste_type')->nullable();
            $table->string('preferred_waste_collection_frequency')->nullable();

            $table->enum('has_temporary_waste_storage', ['yes', 'no'])->default('no');
            $table->string('waste_storage_method')->nullable();

            $table->date('date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->tinyInteger('trash')->default(0)->comment('0=not deleted, 1=deleted');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
