<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lote', 30)->unique();
            $table->foreignId('proveedor_id')->constrained('proveedor')->restrictOnDelete();
            $table->foreignId('sucursal_destino_id')->constrained('sucursal')->restrictOnDelete();
            $table->date('fecha_compra');
            $table->decimal('precio_por_libra', 8, 2);
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('activo'); // 'activo' | 'cerrado'
            $table->foreignId('usuario_id')->nullable()->constrained('usuario')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('costal', function (Blueprint $table) {
            $table->unsignedBigInteger('lote_id')->nullable()->after('id');
            $table->foreign('lote_id')->references('id')->on('lote')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('costal', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropColumn('lote_id');
        });

        Schema::dropIfExists('lote');
    }
};
