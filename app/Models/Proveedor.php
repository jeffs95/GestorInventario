<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';

    protected $fillable = ['nombre', 'telefono', 'direccion', 'nit', 'notas', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function costales()
    {
        return $this->hasMany(Costal::class);
    }
}
