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
        Schema::disableForeignKeyConstraints();

        Schema::create('lst_vehicule', function (Blueprint $table) {
            $table->increments('lsv_no');
            $table->timestamp('lsv_depart');
            $table->timestamp('lsv_arrivee')->nullable();
            $table->boolean('lsv_present');

            $table->unsignedInteger('lsv_veh_no'); 
            $table->foreign('lsv_veh_no')->references('veh_no')->on('vehicule')->onDelete('cascade');

            $table->unsignedInteger('lsv_int_no'); 
            $table->foreign('lsv_int_no')->references('int_no')->on('intervention')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lst_vehicule');
    }
};
