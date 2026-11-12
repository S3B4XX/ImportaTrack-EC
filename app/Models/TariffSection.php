<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Sección del Arancel (I … XXI). */
class TariffSection extends Model
{
    protected $fillable = ['code', 'name'];

    public function chapters(): HasMany
    {
        return $this->hasMany(TariffChapter::class)->orderBy('code');
    }
}
