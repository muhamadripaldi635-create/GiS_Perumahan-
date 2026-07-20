<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use App\Models\Risiko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRisikoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Risiko::with('perumahan')->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_risiko', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('tingkat', 'like', "%{$search}%")
                    ->orWhereHas('perumahan', fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('kecamatan', 'like', "%{$search}%"));
            });
        }

        $risikos = $query->paginate(12)->withQueryString();

        return view('admin.risikos.index', compact('risikos'));
    }

    public function create(): View
    {
        $risiko = new Risiko(['tingkat' => 'rendah', 'status_tindak_lanjut' => 'Terpantau']);
        $perumahans = Perumahan::orderBy('nama')->get(['id', 'nama', 'kecamatan']);

        return view('admin.risikos.create', compact('risiko', 'perumahans'));
    }

    public function store(Request $request): RedirectResponse
    {
        Risiko::create($this->validated($request));

        return redirect()->route('admin.risikos.index')->with('success', 'Data risiko berhasil ditambahkan.');
    }

    public function edit(Risiko $risiko): View
    {
        $perumahans = Perumahan::orderBy('nama')->get(['id', 'nama', 'kecamatan']);

        return view('admin.risikos.edit', compact('risiko', 'perumahans'));
    }

    public function update(Request $request, Risiko $risiko): RedirectResponse
    {
        $risiko->update($this->validated($request));

        return redirect()->route('admin.risikos.index')->with('success', 'Data risiko berhasil diperbarui.');
    }

    public function destroy(Risiko $risiko): RedirectResponse
    {
        $risiko->delete();

        return redirect()->route('admin.risikos.index')->with('success', 'Data risiko berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'perumahan_id' => ['required', 'exists:perumahans,id'],
            'nama_risiko' => ['required', 'string', 'max:180'],
            'tipe' => ['nullable', 'string', 'max:120'],
            'tipe_manual' => ['nullable', 'string', 'max:120'],
            'tingkat' => ['required', 'in:rendah,sedang,tinggi,kritis'],
            'deskripsi' => ['nullable', 'string'],
            'mitigasi' => ['nullable', 'string'],
            'status_tindak_lanjut' => ['nullable', 'string', 'max:120'],
        ]);

        if (($data['tipe'] ?? '') === '__manual__') {
            $data['tipe'] = trim((string) ($data['tipe_manual'] ?? '')) ?: 'Lainnya';
        }

        unset($data['tipe_manual']);

        return $data;
    }
}
