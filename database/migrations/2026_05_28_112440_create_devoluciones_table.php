<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('venta')->cascadeOnDelete();
            $table->foreignId('zapato_id')->constrained('zapato')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursal')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuario')->nullOnDelete();
            $table->string('motivo');                          // no_sirve | talla_incorrecta | cambio_opinion | otro
            $table->text('notas')->nullable();
            $table->decimal('monto_devuelto', 10, 2)->default(0);
            $table->boolean('regresa_inventario')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
