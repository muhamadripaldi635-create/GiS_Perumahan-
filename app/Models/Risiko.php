<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risiko extends Model
{
    use HasFactory;

    protected $fillable = [
        'perumahan_id', 'nama_risiko', 'tipe', 'tingkat', 'deskripsi', 'mitigasi', 'status_tindak_lanjut',
    ];

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class);
    }
}
