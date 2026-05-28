<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run()
    {
        // Categorías
        $categorias = ['Hombre', 'Mujer', 'Niño/a'];
        foreach ($categorias as $nombre) {
            DB::table('categoria_zapato')->insertOrIgnore([
                'nombre'     => $nombre,
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tipos de zapato
        $tipos = ['Tenis', 'Bota', 'Sandalia', 'Zapato Escolar', 'Zapato Formal', 'Zapato de Dama', 'Mocasín', 'Chancla'];
        foreach ($tipos as $nombre) {
            DB::table('tipo_zapato')->insertOrIgnore([
                'nombre'     => $nombre,
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
