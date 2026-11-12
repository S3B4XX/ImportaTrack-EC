<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogos que mantiene el Administrador y que alimentan la liquidación (Core):
 * proveedores, tasas de tributos y el arancel (sección → capítulo → partida).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('country', 60);
            $table->string('email', 200)->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamps();
        });

        // Tabla de parámetros: IVA (15%) y FODINFA (0,5%). No se relaciona; la usa el cálculo.
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 60);
            $table->decimal('rate', 5, 2);
            $table->date('valid_from');
            $table->timestamps();
        });

        // Extensión al ER: estructura del Arancel del Ecuador para el dropdown en cascada.
        Schema::create('tariff_sections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();      // I … XXI
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('tariff_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_section_id')->constrained('tariff_sections')->restrictOnDelete();
            $table->string('code', 2)->unique();      // 01 … 97
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('tariff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_chapter_id')->constrained('tariff_chapters')->restrictOnDelete();
            $table->string('code', 12)->unique();     // subpartida de 10 dígitos, sin puntos
            $table->string('description', 255);
            $table->decimal('ad_valorem_rate', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_items');
        Schema::dropIfExists('tariff_chapters');
        Schema::dropIfExists('tariff_sections');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('suppliers');
    }
};
