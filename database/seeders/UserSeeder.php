<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Usuario administrador
        DB::table('usuario')->insertOrIgnore([
            'id'         => 1,
            'name'       => 'Administrador',
            'email'      => 'admin@pacamanager.com',
            'password'   => Hash::make('secret'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Asignar rol dueño si aún no lo tiene
        $rolDueno = DB::table('rol')->where('slug', 'dueno')->first();
        if ($rolDueno) {
            DB::table('rol_usuario')->insertOrIgnore([
                'usuario_id' => 1,
                'rol_id'     => $rolDueno->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
