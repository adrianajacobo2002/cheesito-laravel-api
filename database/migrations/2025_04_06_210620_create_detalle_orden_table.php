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
        Schema::create('detalle_orden', function (Blueprint $table) {
            $table->id('id_detalle_orden');
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('platillo_id');
            $table->integer('cantidad');
            $table->decimal('subtotal', 8, 2);
            $table->enum('estado', ['en preparación', 'listo'])->default('en preparación');

            $table->foreign('orden_id')->references('id_orden')->on('ordenes')->onDelete('cascade');
            $table->foreign('platillo_id')->references('id_platillo')->on('platillos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden');
    }
};
