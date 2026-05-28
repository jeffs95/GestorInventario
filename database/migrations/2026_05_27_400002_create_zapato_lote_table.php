<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabla de conteo intermedio ───────────────────────────────────
        Schema::create('zapato_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apertura_id')->constrained('apertura')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categoria_zapato');
            $table->foreignId('tipo_id')->constrained('tipo_zapato');
            $table->string('talla', 10);
            // primera_lavado | primera_lustre | regular
            $table->string('clasificacion');
            $table->unsignedInteger('cantidad_contada');
            // cuántos ya fueron registrados individualmente en zapato
            $table->unsignedInteger('cantidad_registrada')->default(0);
            $table->decimal('precio_estimado', 8, 2)->nullable();
            // contado | en_preparacion | completado
            $table->string('estado')->default('contado');
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('usuario');
            $table->timestamps();
        });

        // ── 2. FK en zapato → zapato_lote (para cuando se registren individualmente) ──
        Schema::table('zapato', function (Blueprint $table) {
            $table->unsignedBigInteger('zapato_lote_id')->nullable()->after('apertura_id');
            $table->foreign('zapato_lote_id')
                  ->references('id')
                  ->on('zapato_lote')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zapato', function (Blueprint $table) {
            $table->dropForeign(['zapato_lote_id']);
            $table->dropColumn('zapato_lote_id');
        });
        Schema::dropIfExists('zapato_lote');
    }
};
