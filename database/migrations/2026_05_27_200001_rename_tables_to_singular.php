<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('users',            'usuario');
        Schema::rename('sucursales',       'sucursal');
        Schema::rename('proveedores',      'proveedor');
        Schema::rename('categorias_zapato','categoria_zapato');
        Schema::rename('tipos_zapato',     'tipo_zapato');
        Schema::rename('costales',         'costal');
        Schema::rename('zapatos',          'zapato');
        // bitacora ya está en singular
    }

    public function down(): void
    {
        Schema::rename('usuario',          'users');
        Schema::rename('sucursal',         'sucursales');
        Schema::rename('proveedor',        'proveedores');
        Schema::rename('categoria_zapato', 'categorias_zapato');
        Schema::rename('tipo_zapato',      'tipos_zapato');
        Schema::rename('costal',           'costales');
        Schema::rename('zapato',           'zapatos');
    }
};
