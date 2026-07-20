<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Perumahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'slug', 'deskripsi', 'alamat', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
        'latitude', 'longitude', 'harga_min', 'harga_max', 'luas', 'jumlah_unit', 'developer',
        'status', 'jenis_perumahan', 'fasilitas', 'telepon', 'email', 'website_url', 'google_maps_url',
        'is_recommended',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'harga_min' => 'integer',
            'harga_max' => 'integer',
            'luas' => 'decimal:2',
            'jumlah_unit' => 'integer',
            'fasilitas' => 'array',
            'is_recommended' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(PerumahanImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function risikos(): HasMany
    {
        return $this->hasMany(Risiko::class)->latest();
    }

    public function coverImage(): ?PerumahanImage
    {
        return $this->images->first();
    }

    public function googleMapsDirectUrl(): ?string
    {
        if ($this->google_maps_url) {
            return $this->google_maps_url;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return 'https://www.google.com/maps/search/?api=1&query=' . $this->latitude . ',' . $this->longitude;
        }

        return null;
    }

    public static function hasRecommendationColumn(): bool
    {
        return Schema::hasColumn((new static())->getTable(), 'is_recommended');
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'perumahan';
        $slug = $base;
        $counter = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
