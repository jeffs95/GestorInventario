<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabla principal de apertura ──────────────────────────────────
        Schema::create('apertura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lote')->cascadeOnDelete();
            $table->date('fecha');
            $table->text('notas')->nullable();
            // abierta | clasificada
            $table->string('estado')->default('abierta');
            $table->foreignId('usuario_id')->constrained('usuario');
            $table->timestamps();
        });

        // ── 2. Pivot apertura ↔ costal ──────────────────────────────────────
        Schema::create('apertura_costal', function (Blueprint $table) {
            $table->foreignId('apertura_id')
                  ->constrained('apertura')
                  ->cascadeOnDelete();
            $table->foreignId('costal_id')
                  ->constrained('costal')
                  ->cascadeOnDelete();
            $table->primary(['apertura_id', 'costal_id']);
        });

        // ── 3. Modificar zapato: costal_id nullable + apertura_id ──────────
        // DROP NOT NULL en PostgreSQL sin doctrine/dbal
        DB::statement('ALTER TABLE zapato ALTER COLUMN costal_id DROP NOT NULL');

        Schema::table('zapato', function (Blueprint $table) {
            $table->unsignedBigInteger('apertura_id')
                  ->nullable()
                  ->after('costal_id');

            $table->foreign('apertura_id')
                  ->references('id')
                  ->on('apertura')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('zapato', function (Blueprint $table) {
            $table->dropForeign(['apertura_id']);
            $table->dropColumn('apertura_id');
        });

        DB::statement('ALTER TABLE zapato ALTER COLUMN costal_id SET NOT NULL');

        Schema::dropIfExists('apertura_costal');
        Schema::dropIfExists('apertura');
    }
};
