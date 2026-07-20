<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $perumahans = Perumahan::with(['images', 'risikos'])->latest()->get();
        $extraKecamatans = collect(['Cisaat', 'Cikembar', 'Kelapanunggal', 'Parakansalak', 'Cidahu']);
        $kecamatans = $perumahans->pluck('kecamatan')->filter()->concat($extraKecamatans)->unique()->sort()->values();
        $riskTypes = ['banjir' => 'Banjir', 'longsor' => 'Longsor'];
        $recommendedPerumahans = collect();
        if (Perumahan::hasRecommendationColumn()) {
            $recommendedPerumahans = $perumahans->where('is_recommended', true)->take(3);
        }

        if ($recommendedPerumahans->isEmpty()) {
            $recommendedPerumahans = $perumahans
                ->sortBy(fn ($item) => [$item->risikos->count(), $item->harga_min ?? 999999999])
                ->take(3);
        }

        return view('public.home', compact('perumahans', 'kecamatans', 'recommendedPerumahans', 'riskTypes'));
    }

    public function show(Perumahan $perumahan): View
    {
        $perumahan->load(['images', 'risikos']);

        return view('public.show', compact('perumahan'));
    }
}
