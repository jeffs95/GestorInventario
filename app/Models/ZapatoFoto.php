<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZapatoFoto extends Model
{
    protected $table = 'zapato_fotos';

    protected $fillable = ['zapato_id', 'foto_path', 'orden'];

    public function zapato()
    {
        return $this->belongsTo(Zapato::class);
    }
}
