<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursal';

    protected $fillable = ['nombre', 'direccion', 'telefono', 'encargado_id', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function encargado()
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }

    public function costales()
    {
        return $this->hasMany(Costal::class, 'sucursal_destino_id');
    }

    public function zapatos()
    {
        return $this->hasMany(Zapato::class);
    }

    public function zapatosEnInventario()
    {
        return $this->hasMany(Zapato::class)->where('estado', 'en_inventario');
    }
}
