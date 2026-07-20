<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminRekomendasiPerumahanController extends Controller
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

        if ($hasRecommendedColumn) {
            $query->where('is_recommended', true);
        } else {
            $query->whereRaw('0 = 1');
        }

        $perumahans = $query->paginate(10)->withQueryString();
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
        $growthPercentage = $this->calculateGrowthPercentage();
        $pendingRequests = 0;
        $pendingStatus = 'Butuh review segera';

        return view('admin.perumahans.rekomendasi', compact(
            'perumahans', 'totalPerumahan', 'recommendedTotal', 'kecamatanOptions',
            'statusOptions', 'hasRecommendedColumn', 'growthPercentage',
            'pendingRequests', 'pendingStatus'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $hasRecommendedColumn = Perumahan::hasRecommendationColumn();

        $perumahans = Perumahan::with('images')->when($request->filled('q'), function ($query) use ($request) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('jenis_perumahan', 'like', "%{$search}%");
            });
        })->when($request->filled('kecamatan'), function ($query) use ($request) {
            $query->where('kecamatan', $request->query('kecamatan'));
        })->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->query('status'));
        });

        if ($hasRecommendedColumn) {
            $perumahans->where('is_recommended', true);
        }

        $filename = 'rekomendasi-perumahan-' . now()->format('Ymd-His') . '.csv';

        return new StreamedResponse(function () use ($perumahans) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama', 'Kecamatan', 'Kabupaten', 'Harga Min', 'Harga Max', 'Status', 'Rekomendasi']);

            foreach ($perumahans->cursor() as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->nama,
                    $item->kecamatan,
                    $item->kabupaten,
                    $item->harga_min,
                    $item->harga_max,
                    $item->status,
                    $item->is_recommended ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function calculateGrowthPercentage(): float
    {
        $currentPeriod = Perumahan::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $previousPeriod = Perumahan::whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(31)])->count();

        if ($previousPeriod === 0) {
            return $currentPeriod > 0 ? 100 : 0;
        }

        return round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 1);
    }
}
