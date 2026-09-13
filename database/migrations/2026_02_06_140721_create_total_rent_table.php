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
        Schema::create('total_rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('total')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive, 2=expired');
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
        Schema::dropIfExists('total_rent');
    }
};
