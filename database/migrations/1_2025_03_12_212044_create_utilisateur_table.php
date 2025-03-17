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

        Schema::create('utilisateur', function (Blueprint $table) {
            $table->increments('uti_no');
            $table->string('uti_nom');
            $table->string('uti_prenom');
            $table->boolean('uti_disponible');
            $table->bigInteger('uti_use_id')->unsigned();
            $table->foreign('uti_use_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur');
    }
};
