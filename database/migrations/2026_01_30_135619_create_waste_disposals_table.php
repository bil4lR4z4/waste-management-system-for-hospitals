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
        Schema::create('waste_disposals', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('vehicle_no');
            $table->unsignedBigInteger('city_id');
            $table->decimal('infections', 8, 2)->nullable();
            $table->integer('infections_bags')->nullable();
            $table->decimal('sharp', 8, 2)->nullable();
            $table->integer('sharp_bags')->nullable();
            $table->decimal('pathological', 8, 2)->default(0);
            $table->integer('pathological_bags')->nullable();
            $table->decimal('chemical', 8, 2)->default(0);
            $table->integer('chemical_bags')->nullable();
            $table->decimal('pharmaceutical', 8, 2)->default(0);
            $table->integer('pharmaceutical_bags')->nullable();

            $table->integer('total_bags')->nullable();
            $table->decimal('total_weight', 8, 2);

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
        Schema::dropIfExists('waste_disposals');
    }
};
