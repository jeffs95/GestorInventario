<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'rol_usuario', 'rol_id', 'usuario_id')
                    ->withTimestamps();
    }
}
