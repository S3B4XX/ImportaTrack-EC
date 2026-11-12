<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id', 'email', 'first_name', 'last_name', 'password',
        'phone', 'company_name', 'ruc',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** Importaciones donde el usuario es el cliente (importador). */
    public function clientImports(): HasMany
    {
        return $this->hasMany(Import::class, 'client_id');
    }

    /** Importaciones que el usuario registró como operador. */
    public function operatedImports(): HasMany
    {
        return $this->hasMany(Import::class, 'operator_id');
    }

    /** Eventos de seguimiento que registró el usuario. */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->code === Role::ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->role?->code === Role::OPERATOR;
    }

    public function isClient(): bool
    {
        return $this->role?->code === Role::CLIENT;
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    protected function initials(): Attribute
    {
        return Attribute::get(fn () => mb_strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->last_name, 0, 1)));
    }
}
