<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ADMIN = 'ADMIN';
    public const OPERATOR = 'OPERADOR';
    public const CLIENT = 'CLIENTE';

    protected $fillable = ['code', 'name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function idFor(string $code): ?int
    {
        return static::where('code', $code)->value('id');
    }
}
