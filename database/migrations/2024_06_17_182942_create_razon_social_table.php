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
        Schema::create('razon_socials', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nombre_corto');
            $table->integer('eliminado');
            $table->json('colors')->nullable(); // Añadido para almacenar los colores seleccionados
            $table->string('logo')->nullable(); // Añadido para almacenar la ruta del logo
            $table->string('fondo')->nullable(); // Añadido para almacenar la ruta del fondo
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razon_socials');
    }
};
