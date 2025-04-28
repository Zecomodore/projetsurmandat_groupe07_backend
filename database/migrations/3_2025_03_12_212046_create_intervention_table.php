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

        Schema::create('intervention', function (Blueprint $table) {
            $table->increments('int_no');
            $table->date('int_date');
            $table->time('int_heure');
            $table->string('int_description');
            //$table->string('int_adresse');
            //$table->string('int_commentaire');
            $table->boolean('int_en_cours');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention');
    }
};
