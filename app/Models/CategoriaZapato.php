<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaZapato extends Model
{
    protected $table = 'categoria_zapato';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function zapatos()
    {
        return $this->hasMany(Zapato::class, 'categoria_id');
    }
}
