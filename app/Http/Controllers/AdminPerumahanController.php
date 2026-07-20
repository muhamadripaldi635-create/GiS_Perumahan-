<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use App\Models\PerumahanImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPerumahanController extends Controller
{
    public function index(Request $request): View
    {
        $hasRecommendedColumn = Perumahan::hasRecommendationColumn();
        $query = Perumahan::with('images')->withCount('risikos')->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('jenis_perumahan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->query('kecamatan'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($hasRecommendedColumn && $request->query('recommended') === '1') {
            $query->where('is_recommended', true);
        }

        $perumahans = $query->paginate(10)->withQueryString();
        $allPoints = Perumahan::withCount('risikos')->select('id', 'nama', 'latitude', 'longitude', 'status', 'kecamatan')->get();
        $totalPerumahan = Perumahan::count();
        $recommendedTotal = $hasRecommendedColumn ? Perumahan::where('is_recommended', true)->count() : 0;
        $kecamatanOptions = Perumahan::query()
            ->whereNotNull('kecamatan')
            ->distinct('kecamatan')
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        $extraKecamatan = [
            'Cicurug',
            'Cidahu',
            'Parakansalak',
            'Kelapanunggal',
            'Cikembar',
            'Parungkuda',
            'Cisaat',
            'Nagrak',
            'Cibadak',
        ];

        $kecamatanOptions = $kecamatanOptions
            ->concat($extraKecamatan)
            ->unique()
            ->sort()
            ->values();

        $statusOptions = Perumahan::query()
            ->distinct('status')
            ->orderBy('status')
            ->pluck('status');
        $recommendedOnly = $hasRecommendedColumn && $request->query('recommended') === '1';

        return view('admin.perumahans.index', compact(
            'perumahans', 'allPoints', 'totalPerumahan', 'recommendedTotal',
            'kecamatanOptions', 'statusOptions', 'recommendedOnly'
        ));
    }

    public function toggleRecommended(Perumahan $perumahan): RedirectResponse
    {
        if (! Perumahan::hasRecommendationColumn()) {
            return redirect()->back()->with('error', 'Fitur rekomendasi belum tersedia. Silakan jalankan migrasi terlebih dahulu.');
        }

        $perumahan->update(['is_recommended' => ! $perumahan->is_recommended]);

        return redirect()->back()->with('success', 'Status rekomendasi perumahan berhasil diperbarui.');
    }

    public function create(): View
    {
        $perumahan = new Perumahan([
            'kabupaten' => 'Sukabumi',
            'provinsi' => 'Jawa Barat',
            'status' => 'tersedia',
        ]);
        $allPoints = Perumahan::withCount('risikos')->select('id', 'nama', 'latitude', 'longitude', 'status', 'kecamatan')->get();

        return view('admin.perumahans.create', compact('perumahan', 'allPoints'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = Perumahan::uniqueSlug($validated['nama']);
        $validated['fasilitas'] = $this->parseFasilitas($request->input('fasilitas'));

        $perumahan = Perumahan::create($validated);
        $this->storeImages($request, $perumahan);

        return redirect()->route('admin.perumahans.index')->with('success', 'Data perumahan berhasil ditambahkan.');
    }

    public function edit(Perumahan $perumahan): View
    {
        $perumahan->load('images');
        $allPoints = Perumahan::where('id', '!=', $perumahan->id)->withCount('risikos')->select('id', 'nama', 'latitude', 'longitude', 'status', 'kecamatan')->get();

        return view('admin.perumahans.edit', compact('perumahan', 'allPoints'));
    }

    public function update(Request $request, Perumahan $perumahan): RedirectResponse
    {
        $validated = $this->validated($request, $perumahan->id);
        $validated['slug'] = Perumahan::uniqueSlug($validated['nama'], $perumahan->id);
        $validated['fasilitas'] = $this->parseFasilitas($request->input('fasilitas'));

        $perumahan->update($validated);
        $this->deleteSelectedImages($request, $perumahan);
        $this->storeImages($request, $perumahan);

        return redirect()->route('admin.perumahans.edit', $perumahan)->with('success', 'Data perumahan berhasil diperbarui.');
    }

    public function destroy(Perumahan $perumahan): RedirectResponse
    {
        $perumahan->load('images');
        foreach ($perumahan->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $perumahan->delete();

        return redirect()->route('admin.perumahans.index')->with('success', 'Data perumahan berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:180'],
            'deskripsi' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:120'],
            'kecamatan' => ['nullable', 'string', 'max:120'],
            'kabupaten' => ['nullable', 'string', 'max:120'],
            'provinsi' => ['nullable', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'harga_min' => ['nullable', 'integer', 'min:0'],
            'harga_max' => ['nullable', 'integer', 'min:0'],
            'luas' => ['nullable', 'numeric', 'min:0'],
            'jumlah_unit' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:tersedia,proses,terjual,tidak_aktif'],
            'jenis_perumahan' => ['nullable', 'string', 'max:120'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:160'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'google_maps_url' => ['nullable', 'string', 'max:800'],
            'is_recommended' => ['sometimes', 'boolean'],
            'gambar' => ['nullable', 'array'],
            'gambar.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hapus_gambar' => ['nullable', 'array'],
            'hapus_gambar.*' => ['integer'],
        ]);

        $data['is_recommended'] = $request->has('is_recommended');
        unset($data['gambar'], $data['hapus_gambar']);

        return $data;
    }

    private function parseFasilitas(?string $input): array
    {
        if (! $input) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n|,/', $input))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storeImages(Request $request, Perumahan $perumahan): void
    {
        if (! $request->hasFile('gambar')) {
            return;
        }

        $startOrder = $perumahan->images()->max('sort_order') ?? 0;
        foreach ($request->file('gambar') as $index => $file) {
            $path = $file->store('perumahan', 'public');
            $perumahan->images()->create([
                'path' => $path,
                'caption' => $perumahan->nama,
                'sort_order' => $startOrder + $index + 1,
            ]);
        }
    }

    private function deleteSelectedImages(Request $request, Perumahan $perumahan): void
    {
        $ids = $request->input('hapus_gambar', []);
        if (! is_array($ids) || count($ids) === 0) {
            return;
        }

        $images = PerumahanImage::where('perumahan_id', $perumahan->id)->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
