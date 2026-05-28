<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zapato_id')->constrained('zapato')->restrictOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursal')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->restrictOnDelete();
            $table->decimal('precio_lista', 10, 2);   // precio en etiqueta
            $table->decimal('precio_venta', 10, 2);   // precio real cobrado
            $table->string('notas', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta');
    }
};
