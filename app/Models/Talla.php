<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    protected $table = 'talla';

    protected $fillable = ['nombre', 'activo'];

    public function zapatos()
    {
        return $this->hasMany(Zapato::class);
    }
}
