<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';

    protected $fillable = [
        'numero_lote',
        'proveedor_id',
        'sucursal_destino_id',
        'fecha_compra',
        'precio_por_libra',
        'notas',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
    ];

    // ── Eventos ─────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Lote $lote) {
            $lote->numero_lote = static::generarNumero();
        });
    }

    public static function generarNumero(): string
    {
        $hoy        = now()->format('Ymd');
        $correlativo = static::whereDate('created_at', today())->count() + 1;
        return 'L-' . $hoy . '-' . str_pad($correlativo, 3, '0', STR_PAD_LEFT);
    }

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function costales()
    {
        return $this->hasMany(Costal::class);
    }

    public function aperturas()
    {
        return $this->hasMany(Apertura::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getPesoTotalAttribute(): float
    {
        return (float) $this->costales->sum('peso_libras');
    }

    public function getCostoTotalAttribute(): float
    {
        return (float) $this->costales->sum('costo_total');
    }

    public function getTotalCostalesAttribute(): int
    {
        return $this->costales->count();
    }
}
