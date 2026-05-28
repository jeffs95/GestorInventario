<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'about_me',
        'sucursal_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────────────────

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'usuario_id', 'rol_id')
                    ->withTimestamps();
    }

    // ── Helpers de rol ──────────────────────────────────────────────────────

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function isDueno(): bool     { return $this->hasRole('dueno'); }
    public function isEncargado(): bool { return $this->hasRole('encargado'); }
    public function isPreparador(): bool{ return $this->hasRole('preparador'); }

    /** Etiqueta del primer rol asignado (para mostrar en UI) */
    public function getRolLabelAttribute(): string
    {
        return $this->roles->first()?->nombre ?? '—';
    }
}
