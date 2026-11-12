<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Partida (subpartida) arancelaria con su porcentaje Ad Valorem. */
class TariffItem extends Model
{
    use HasFactory;

    protected $fillable = ['tariff_chapter_id', 'code', 'description', 'ad_valorem_rate'];

    protected function casts(): array
    {
        return ['ad_valorem_rate' => 'decimal:2'];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(TariffChapter::class, 'tariff_chapter_id');
    }

    public function importItems(): HasMany
    {
        return $this->hasMany(ImportItem::class);
    }

    /** 8471500000 → 8471.50.00.00 */
    protected function formattedCode(): Attribute
    {
        return Attribute::get(function () {
            $c = $this->code;

            return strlen($c) === 10
                ? substr($c, 0, 4).'.'.substr($c, 4, 2).'.'.substr($c, 6, 2).'.'.substr($c, 8, 2)
                : $c;
        });
    }

    /** Búsqueda por código (con o sin puntos) o por descripción. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        $digits = preg_replace('/\D/', '', $term);

        return $query->where(function (Builder $q) use ($term, $digits) {
            $q->whereRaw('LOWER(description) LIKE ?', ['%'.mb_strtolower($term).'%']);
            if ($digits !== '') {
                $q->orWhere('code', 'like', $digits.'%');
            }
        });
    }
}
