<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerumahanImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'perumahan_id', 'path', 'caption', 'sort_order',
    ];

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class);
    }
}
