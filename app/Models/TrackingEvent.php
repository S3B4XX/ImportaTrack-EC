<?php

namespace App\Models;

use App\Enums\ImportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Evento de seguimiento: cada cambio de estado con su fecha, usuario y nota. */
class TrackingEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['import_id', 'user_id', 'status', 'event_date', 'location', 'note'];

    protected function casts(): array
    {
        return [
            'status' => ImportStatus::class,
            'event_date' => 'datetime',
        ];
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
