<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use App\Models\Risiko;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalPerumahan = Perumahan::count();
        $totalKecamatan = Perumahan::whereNotNull('kecamatan')->distinct('kecamatan')->count('kecamatan');
        $totalRisiko = Risiko::count();
        $risikoTinggi = Risiko::whereIn('tingkat', ['tinggi', 'kritis'])->count();
        $perumahanTerbaru = Perumahan::withCount('risikos')->latest()->take(6)->get();
        $risikoTerbaru = Risiko::with('perumahan')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalPerumahan', 'totalKecamatan', 'totalRisiko', 'risikoTinggi', 'perumahanTerbaru', 'risikoTerbaru'
        ));
    }
}
