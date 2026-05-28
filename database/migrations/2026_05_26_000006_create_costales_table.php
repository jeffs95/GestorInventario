<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costales', function (Blueprint $table) {
            $table->id();
            $table->string('numero_costal', 50)->nullable();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('sucursal_destino_id')->constrained('sucursales');
            $table->decimal('peso_libras', 8, 2);
            $table->decimal('precio_por_libra', 8, 2);
            $table->decimal('costo_total', 10, 2);
            $table->date('fecha_compra');
            // recibido | en_clasificacion | clasificado
            $table->string('estado')->default('recibido');
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costales');
    }
};
