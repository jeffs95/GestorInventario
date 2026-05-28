<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Costal extends Model
{
    protected $table = 'costal';

    protected $fillable = [
        'numero_costal', 'lote_id', 'proveedor_id', 'sucursal_destino_id',
        'peso_libras', 'precio_por_libra', 'costo_total',
        'fecha_compra', 'estado', 'notas', 'usuario_id',
    ];

    protected $casts = ['fecha_compra' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (Costal $costal) {
            $costal->numero_costal = static::generarNumero();
        });

        static::saving(function (Costal $costal) {
            $costal->costo_total = $costal->peso_libras * $costal->precio_por_libra;
        });
    }

    public static function generarNumero(): string
    {
        $hoy        = now()->format('Ymd');
        $correlativo = static::whereDate('created_at', today())->count() + 1;
        return 'C-' . $hoy . '-' . str_pad($correlativo, 3, '0', STR_PAD_LEFT);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function zapatos()
    {
        return $this->hasMany(Zapato::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'recibido'         => 'Recibido',
            'en_clasificacion' => 'En clasificación',
            'clasificado'      => 'Clasificado',
            default            => $this->estado,
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'recibido'         => 'secondary',
            'en_clasificacion' => 'warning',
            'clasificado'      => 'success',
            default            => 'secondary',
        };
    }
}
