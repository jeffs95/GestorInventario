<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';

    protected $fillable = [
        'tipo',
        'mensaje',
        'stack_trace',
        'url',
        'metodo',
        'ip',
        'usuario_id',
    ];

    public static function registrar(string $tipo, string $mensaje, ?string $stackTrace = null): void
    {
        try {
            static::create([
                'tipo'        => $tipo,
                'mensaje'     => $mensaje,
                'stack_trace' => $stackTrace,
                'url'         => request()?->fullUrl(),
                'metodo'      => request()?->method(),
                'ip'          => request()?->ip(),
                'usuario_id'  => auth()->id(),
            ]);
        } catch (\Throwable) {
            // Si falla el log en BD, cae al log de Laravel
            \Illuminate\Support\Facades\Log::error("Bitácora falló al guardar: {$mensaje}");
        }
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
