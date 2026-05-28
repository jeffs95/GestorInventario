<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zapato_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zapato_id')->constrained('zapato')->onDelete('cascade');
            $table->string('foto_path');
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zapato_fotos');
    }
};
