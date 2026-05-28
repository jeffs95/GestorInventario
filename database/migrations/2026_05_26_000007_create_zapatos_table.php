<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zapatos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_unico', 50)->unique();
            $table->foreignId('costal_id')->constrained('costales');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('categoria_id')->constrained('categorias_zapato');
            $table->foreignId('tipo_id')->constrained('tipos_zapato');
            $table->string('talla', 10);
            // regular | primera_lavado | primera_lustre
            $table->string('clasificacion');
            $table->decimal('precio_lista', 8, 2);
            // en_proceso | en_inventario | vendido | devuelto
            $table->string('estado')->default('en_inventario');
            $table->string('foto_path', 255)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zapatos');
    }
};
