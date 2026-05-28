<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZapatoLote extends Model
{
    protected $table = 'zapato_lote';

    protected $fillable = [
        'apertura_id',
        'categoria_id',
        'tipo_id',
        'talla',
        'clasificacion',
        'cantidad_contada',
        'cantidad_registrada',
        'precio_estimado',
        'estado',
        'notas',
        'usuario_id',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function apertura()
    {
        return $this->belongsTo(Apertura::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaZapato::class, 'categoria_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoZapato::class, 'tipo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /** Zapatos individuales ya registrados de este lote */
    public function zapatos()
    {
        return $this->hasMany(Zapato::class);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getPendientesAttribute(): int
    {
        return $this->cantidad_contada - $this->cantidad_registrada;
    }

    public function getEstaCompletadoAttribute(): bool
    {
        return $this->cantidad_registrada >= $this->cantidad_contada;
    }

    public function getClasificacionLabelAttribute(): string
    {
        return match ($this->clasificacion) {
            'primera_lavado' => 'Primera — Lavado',
            'primera_lustre' => 'Primera — Lustre',
            'regular'        => 'Regular',
            default          => $this->clasificacion,
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'contado'        => 'warning',
            'en_preparacion' => 'info',
            'completado'     => 'success',
            default          => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'contado'        => 'Contado',
            'en_preparacion' => 'En preparación',
            'completado'     => 'Completado',
            default          => $this->estado,
        };
    }
}
