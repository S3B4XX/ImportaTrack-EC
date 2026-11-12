<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Capítulo del Arancel (01 … 97). Los 2 primeros dígitos de toda partida. */
class TariffChapter extends Model
{
    protected $fillable = ['tariff_section_id', 'code', 'name'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(TariffSection::class, 'tariff_section_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TariffItem::class);
    }
}
