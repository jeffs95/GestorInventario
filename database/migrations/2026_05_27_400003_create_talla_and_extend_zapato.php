<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Catálogo de tallas ───────────────────────────────────────────
        Schema::create('talla', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique(); // "36", "42", "S", "M", etc.
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Seed de tallas más comunes
        $tallas = [
            // Infantil
            '16','17','18','19','20','21','22','23','24','25',
            // Dama
            '35','36','37','38','39','40','41',
            // Caballero
            '42','43','44','45','46',
            // Tallas por letra
            'S','M','L','XL','XXL',
        ];
        foreach ($tallas as $t) {
            DB::table('talla')->insert(['nombre' => $t, 'activo' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        // ── 2. Nuevos atributos en zapato ───────────────────────────────────
        // Hacer talla (varchar) nullable para compatibilidad hacia atrás
        DB::statement('ALTER TABLE zapato ALTER COLUMN talla DROP NOT NULL');

        Schema::table('zapato', function (Blueprint $table) {
            // FK al catálogo de tallas (opcional)
            $table->unsignedBigInteger('talla_id')->nullable()->after('tipo_id');
            $table->foreign('talla_id')->references('id')->on('talla')->nullOnDelete();

            // Atributos opcionales del zapato
            $table->string('color', 50)->nullable()->after('talla_id');
            $table->string('marca', 80)->nullable()->after('color');
            // hombre | mujer | nino | nina | unisex
            $table->string('genero', 20)->nullable()->after('marca');
            // muy_bueno | bueno | regular
            $table->string('condicion', 20)->nullable()->after('genero');
        });
    }

    public function down(): void
    {
        Schema::table('zapato', function (Blueprint $table) {
            $table->dropForeign(['talla_id']);
            $table->dropColumn(['talla_id', 'color', 'marca', 'genero', 'condicion']);
        });
        DB::statement('ALTER TABLE zapato ALTER COLUMN talla SET NOT NULL');
        Schema::dropIfExists('talla');
    }
};
