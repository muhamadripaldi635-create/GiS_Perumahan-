<?php

namespace Database\Seeders;

use App\Models\Perumahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GeojsonPerumahanSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/perumahan.geojson');
        if (! file_exists($path)) {
            return;
        }

        $json = json_decode(file_get_contents($path), true);
        $features = Arr::get($json, 'features', []);

        foreach ($features as $feature) {
            $props = Arr::get($feature, 'properties', []);
            $coords = Arr::get($feature, 'geometry.coordinates', []);
            if (! is_array($coords) || count($coords) < 2) {
                continue;
            }

            $lng = (float) $coords[0];
            $lat = (float) $coords[1];
            if (! $this->validCoordinate($lat, $lng)) {
                continue;
            }

            $nama = trim((string) ($props['nama'] ?? $props['name'] ?? 'Perumahan Tanpa Nama'));
            if ($nama === '') {
                continue;
            }

            $slugBase = Str::slug($nama) ?: 'perumahan';
            $slug = $slugBase;
            $counter = 2;
            while (Perumahan::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter++;
            }

            Perumahan::create([
                'nama' => $nama,
                'slug' => $slug,
                'deskripsi' => $props['deskripsi'] ?? null,
                'alamat' => $props['alamat'] ?? $props['jalan'] ?? null,
                'kelurahan' => $props['desa'] ?? $props['kelurahan'] ?? null,
                'kecamatan' => $props['kecamatan'] ?? null,
                'kabupaten' => $props['kabupaten'] ?? 'Sukabumi',
                'provinsi' => $props['provinsi'] ?? 'Jawa Barat',
                'latitude' => $lat,
                'longitude' => $lng,
                'harga_min' => $this->toInt($props['harga_min'] ?? $props['hargaMin'] ?? null),
                'harga_max' => $this->toInt($props['harga_max'] ?? $props['hargaMax'] ?? null),
                'developer' => $props['developer'] ?? null,
                'status' => $this->normalizeStatus($props['status'] ?? 'tersedia'),
                'jenis_perumahan' => $props['jenis'] ?? $props['jenis_perumahan'] ?? null,
                'fasilitas' => ['Akses jalan', 'Area hunian', 'Lingkungan sekitar'],
            ]);
        }
    }

    private function validCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180 && ! ($lat == 0.0 && $lng == 0.0);
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function normalizeStatus(mixed $status): string
    {
        $value = Str::of((string) $status)->lower()->replace(' ', '_')->toString();
        return in_array($value, ['tersedia', 'proses', 'terjual', 'tidak_aktif'], true) ? $value : 'tersedia';
    }
}
