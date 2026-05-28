<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Catálogo de roles
        Schema::create('rol', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('slug', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 2. Roles por defecto
        $now = now();
        DB::table('rol')->insert([
            ['nombre' => 'Dueño',      'slug' => 'dueno',      'descripcion' => 'Acceso total al sistema',         'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Encargado',  'slug' => 'encargado',  'descripcion' => 'Gestión de su sucursal asignada', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Preparador', 'slug' => 'preparador', 'descripcion' => 'Preparación de zapatos',          'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Tabla pivote rol_usuario
        Schema::create('rol_usuario', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('rol')->cascadeOnDelete();
            $table->primary(['usuario_id', 'rol_id']);
            $table->timestamps();
        });

        // 4. Migrar asignaciones existentes (columna role → pivot)
        $roles = DB::table('rol')->pluck('id', 'slug');
        $usuarios = DB::table('usuario')->whereNotNull('role')->get(['id', 'role']);
        foreach ($usuarios as $u) {
            if (isset($roles[$u->role])) {
                DB::table('rol_usuario')->insertOrIgnore([
                    'usuario_id' => $u->id,
                    'rol_id'     => $roles[$u->role],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 5. Eliminar columna role de usuario
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        // Restaurar columna role
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('role', 30)->default('encargado')->after('password');
        });

        // Migrar de vuelta del pivot (solo el primer rol)
        $roles = DB::table('rol')->pluck('slug', 'id');
        $pivots = DB::table('rol_usuario')->get();
        foreach ($pivots as $p) {
            DB::table('usuario')->where('id', $p->usuario_id)
                ->update(['role' => $roles[$p->rol_id] ?? 'encargado']);
        }

        Schema::dropIfExists('rol_usuario');
        Schema::dropIfExists('rol');
    }
};
