<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoZapato extends Model
{
    protected $table = 'tipo_zapato';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function zapatos()
    {
        return $this->hasMany(Zapato::class, 'tipo_id');
    }
}
