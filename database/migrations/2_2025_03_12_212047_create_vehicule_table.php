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

        Schema::create('vehicule', function (Blueprint $table) {
            $table->increments('veh_no');
            $table->string('veh_nom');
            $table->boolean('veh_disponible');
            $table->bigInteger('veh_use_id')->unsigned();
            $table->foreign('veh_use_id')->references('id')->on('users');

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicule');
    }
};
