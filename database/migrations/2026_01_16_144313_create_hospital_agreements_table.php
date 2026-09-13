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
        Schema::create('hospital_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->integer('number_of_beds')->default(0);

            $table->string('waste_generation_volume')->nullable();
            $table->string('waste_type')->nullable();
            $table->string('preferred_waste_collection_frequency')->nullable();

            $table->enum('has_temporary_waste_storage', ['yes', 'no'])->default('no');
            $table->string('waste_storage_method')->nullable();

            $table->date('date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->string('document')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('trash')->default(0)->comment('0=not deleted, 1=deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_agreements');
    }
};
