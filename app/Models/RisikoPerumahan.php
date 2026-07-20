<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RisikoPerumahan extends Model
{
    use HasFactory;

    protected $table = 'risiko_perumahans';

    protected $fillable = [
        'perumahan_id',
        'tipe',
        'tingkat',
        'deskripsi',
        'mitigasi',
        'status_tindak_lanjut',
    ];

    public const TINGKAT = ['rendah', 'sedang', 'tinggi', 'kritis'];

    public const STATUS_TINDAK_LANJUT = ['belum_ditinjau', 'dipantau', 'ditindaklanjuti', 'selesai'];

    public const TIPE_OPTIONS = [
        'Banjir',
        'Longsor',
        'Akses Jalan Rawan',
        'Drainase Buruk',
        'Kepadatan Lingkungan',
        'Lainnya',
    ];

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class);
    }

    public function getTingkatLabelAttribute(): string
    {
        return match ($this->tingkat) {
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'kritis' => 'Kritis',
            default => ucfirst((string) $this->tingkat),
        };
    }
}
