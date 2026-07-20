<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLaporanController extends Controller
{
    public function index(Request $request): View
    {
        $kecamatans = Perumahan::whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
        $selectedKecamatan = $request->query('kecamatan');
        $perumahans = Perumahan::with(['risikos', 'images'])
            ->when($selectedKecamatan, fn ($query) => $query->where('kecamatan', $selectedKecamatan))
            ->orderBy('kecamatan')
            ->orderBy('nama')
            ->get();

        return view('admin.laporan.index', compact('kecamatans', 'selectedKecamatan', 'perumahans'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedKecamatan = $request->query('kecamatan');
        $perumahans = Perumahan::with('risikos')
            ->when($selectedKecamatan, fn ($query) => $query->where('kecamatan', $selectedKecamatan))
            ->orderBy('kecamatan')
            ->orderBy('nama')
            ->get();

        $filename = 'laporan-perumahan-' . Str::slug($selectedKecamatan ?: 'semua-kecamatan') . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($perumahans) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama Perumahan', 'Kecamatan', 'Alamat', 'Status', 'Jumlah Unit', 'Harga Min', 'Harga Max', 'Telepon', 'Email', 'Google Maps', 'Jumlah Risiko', 'Daftar Risiko']);
            foreach ($perumahans as $item) {
                fputcsv($handle, [
                    $item->nama,
                    $item->kecamatan,
                    $item->alamat,
                    $item->status,
                    $item->jumlah_unit,
                    $item->harga_min,
                    $item->harga_max,
                    $item->telepon,
                    $item->email,
                    $item->googleMapsDirectUrl(),
                    $item->risikos->count(),
                    $item->risikos->map(fn ($r) => $r->nama_risiko . ' (' . $r->tingkat . ')')->implode('; '),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
