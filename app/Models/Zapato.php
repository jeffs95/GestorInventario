<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zapato extends Model
{
    protected $table = 'zapato';

    protected $fillable = [
        'codigo_unico', 'costal_id', 'apertura_id', 'zapato_lote_id', 'sucursal_id',
        'categoria_id', 'tipo_id', 'talla_id', 'talla',
        'color', 'marca', 'genero', 'condicion',
        'clasificacion', 'precio_lista', 'estado',
        'foto_path', 'notas', 'usuario_id',
    ];

    /**
     * Genera un código único para el zapato.
     *
     * $ref puede ser:
     *  - El id del costal como string (clasificación individual):  "5"  → REG-5-0001
     *  - "A{id}" para apertura (clasificación masiva):           "A3"  → REG-A3-0001
     */
    public static function generarCodigo(string $ref, string $clasificacion): string
    {
        $prefijo = match ($clasificacion) {
            'regular'         => 'REG',
            'primera_lavado'  => 'PL',
            'primera_lustre'  => 'PLU',
            default           => 'ZAP',
        };

        $ultimo = static::where('codigo_unico', 'like', "{$prefijo}-{$ref}-%")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->codigo_unico);
            $seq = ((int) end($partes)) + 1;
        }

        return "{$prefijo}-{$ref}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function costal()
    {
        return $this->belongsTo(Costal::class);
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class);
    }

    public function apertura()
    {
        return $this->belongsTo(Apertura::class);
    }

    public function zapatoLote()
    {
        return $this->belongsTo(ZapatoLote::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
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

    public function venta()
    {
        return $this->hasOne(Venta::class);
    }

    public function fotos()
    {
        return $this->hasMany(ZapatoFoto::class)->orderBy('orden');
    }

    public function getClasificacionLabelAttribute(): string
    {
        return match ($this->clasificacion) {
            'regular'         => 'Regular',
            'primera_lavado'  => 'Primera (Lavado)',
            'primera_lustre'  => 'Primera (Lustre)',
            default           => $this->clasificacion,
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'pendiente_precio' => 'warning',
            'en_proceso'       => 'warning',
            'en_inventario'    => 'success',
            'vendido'          => 'info',
            'devuelto'         => 'secondary',
            default            => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'pendiente_precio' => 'Sin precio',
            'en_proceso'       => 'En proceso',
            'en_inventario'    => 'En inventario',
            'vendido'          => 'Vendido',
            'devuelto'         => 'Devuelto',
            default            => $this->estado,
        };
    }
}
