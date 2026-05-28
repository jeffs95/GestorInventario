<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    protected $fillable = [
        'zapato_id', 'sucursal_id', 'usuario_id',
        'precio_lista', 'precio_venta', 'notas',
    ];

    protected $casts = [
        'precio_lista' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function zapato()
    {
        return $this->belongsTo(Zapato::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Diferencia entre precio lista y precio real cobrado */
    public function getDiferenciaAttribute(): float
    {
        return (float) $this->precio_venta - (float) $this->precio_lista;
    }
}
