<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'venta_id', 'zapato_id', 'sucursal_id', 'usuario_id',
        'motivo', 'notas', 'monto_devuelto', 'regresa_inventario',
    ];

    protected $casts = [
        'monto_devuelto'    => 'decimal:2',
        'regresa_inventario' => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

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

    // ── Accessors ───────────────────────────────────────────────────────────

    public function getMotivoLabelAttribute(): string
    {
        return match ($this->motivo) {
            'no_sirve'         => 'No sirve / Defectuoso',
            'talla_incorrecta' => 'Talla incorrecta',
            'cambio_opinion'   => 'Cambio de opinión',
            'otro'             => 'Otro',
            default            => $this->motivo,
        };
    }
}
