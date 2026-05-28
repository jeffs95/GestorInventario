<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20)->default('error'); // error | warning | info
            $table->text('mensaje');
            $table->text('stack_trace')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('metodo', 10)->nullable();
            $table->string('ip', 50)->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
