<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    protected $table = 'apertura';

    protected $fillable = [
        'lote_id',
        'fecha',
        'notas',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function costales()
    {
        return $this->belongsToMany(Costal::class, 'apertura_costal');
    }

    /** Zapatos individuales de primera ya preparados y registrados */
    public function zapatos()
    {
        return $this->hasMany(Zapato::class);
    }

    /** Conteos intermedios de primera (zapato_lote) */
    public function zapatoLotes()
    {
        return $this->hasMany(ZapatoLote::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Número de zapatos de primera registrados en esta apertura */
    public function getTotalZapatosAttribute(): int
    {
        return $this->zapatos->count();
    }

    /** Stats de clasificación:
     *  - Regulares: conteo directo de zapatos en_inventario
     *  - Primera: suma de cantidades en zapato_lote (conteo intermedio)
     */
    public function getStatsAttribute(): array
    {
        $lotes = $this->zapatoLotes;
        $zaps  = $this->zapatos;

        return [
            // regulares: zapatos individuales ya en inventario
            'regulares'         => $zaps->where('clasificacion', 'regular')->count(),
            // primera: suma de cantidades contadas en zapato_lote
            'primera_lavado'    => $lotes->where('clasificacion', 'primera_lavado')->sum('cantidad_contada'),
            'primera_lustre'    => $lotes->where('clasificacion', 'primera_lustre')->sum('cantidad_contada'),
            // total de primera ya preparada e ingresada como zapato individual
            'primera_preparada' => $zaps->whereIn('clasificacion', ['primera_lavado','primera_lustre'])->count(),
            // lotes de primera pendientes de preparar
            'lotes_pendientes'  => $lotes->where('estado', '!=', 'completado')->count(),
        ];
    }
}
