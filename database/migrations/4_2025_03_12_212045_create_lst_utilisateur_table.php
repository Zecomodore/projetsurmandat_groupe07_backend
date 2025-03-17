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

        Schema::create('lst_utilisateur', function (Blueprint $table) {
            $table->increments('lsu_no');
            $table->unsignedInteger('lsu_uti_no');
            $table->foreign('lsu_uti_no')->references('uti_no')->on('utilisateur')->onDelete('cascade');

            $table->unsignedInteger('lsu_int_no'); 
            $table->foreign('lsu_int_no')->references('int_no')->on('intervention')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lst_utilisateur');
    }
};
