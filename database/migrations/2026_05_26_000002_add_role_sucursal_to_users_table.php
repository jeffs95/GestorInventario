<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('encargado')->after('about_me');
            $table->foreignId('sucursal_id')
                ->nullable()
                ->constrained('sucursales')
                ->nullOnDelete()
                ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['role', 'sucursal_id']);
        });
    }
};
