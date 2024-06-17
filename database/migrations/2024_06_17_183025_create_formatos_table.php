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
        Schema::create('formatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruta_pdf');
            $table->string('convertio_id');
            $table->string('ruta_html');
            $table->integer('eliminado');
            $table->foreignId('elementos_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formatos');
    }
};