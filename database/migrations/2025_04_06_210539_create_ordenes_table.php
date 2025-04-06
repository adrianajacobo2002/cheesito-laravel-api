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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id('id_orden');
            $table->dateTime('fecha');
            $table->enum('estado', ['por pagar', 'cancelado'])->default('por pagar');
            $table->string('nombre_cliente')->nullable();
            $table->unsignedBigInteger('mesero_id');
            $table->unsignedBigInteger('mesa_id');

            $table->foreign('mesero_id')->references('id_mesero')->on('meseros')->onDelete('cascade');
            $table->foreign('mesa_id')->references('id_mesa')->on('mesas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
