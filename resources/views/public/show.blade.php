@extends('layouts.public')

@section('title', $perumahan->nama . ' - Detail Perumahan')

@section('content')
@php
    $mapsUrl = $perumahan->googleMapsDirectUrl();
    $rupiah = fn ($v) => $v ? 'Rp ' . number_format($v, 0, ',', '.') : '-';
    $statusLabel = ['tersedia' => 'Tersedia', 'proses' => 'Proses', 'terjual' => 'Terjual', 'tidak_aktif' => 'Tidak Aktif'][$perumahan->status] ?? '-';
@endphp

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="{{ route('home') }}#data" class="mb-6 inline-flex items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-sm font-black text-slate-600 hover:text-blue-600">← Kembali ke Data</a>
    <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
        <main class="space-y-7">
            <div class="overflow-hidden rounded-[2rem] border border-line bg-white shadow-zenit">
                @if ($perumahan->images->count())
                    <img src="{{ asset('storage/' . $perumahan->images->first()->path) }}" class="h-[420px] w-full object-cover" alt="{{ $perumahan->nama }}">
                @else
                    <div class="grid h-[360px] place-items-center bg-soft text-sm font-black uppercase tracking-wider text-slate-400">Gambar belum tersedia</div>
                @endif
                <div class="p-6 md:p-8">
                    <p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Detail Perumahan</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight md:text-5xl">{{ $perumahan->nama }}</h1>
                    <p class="mt-4 max-w-3xl leading-8 text-slate-600">{{ $perumahan->deskripsi ?: 'Deskripsi perumahan belum tersedia.' }}</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($mapsUrl)<a href="{{ $mapsUrl }}" target="_blank" class="rounded-full btn-blue px-5 py-3 text-sm font-black">Buka Google Maps</a>@endif
                        @if($perumahan->telepon)<a href="tel:{{ $perumahan->telepon }}" class="rounded-full border border-line bg-white px-5 py-3 text-sm font-black text-ink">Telepon</a>@endif
                        @if($perumahan->email)<a href="mailto:{{ $perumahan->email }}" class="rounded-full border border-line bg-white px-5 py-3 text-sm font-black text-ink">Email</a>@endif
                        @if($perumahan->website_url)<a href="{{ $perumahan->website_url }}" target="_blank" class="rounded-full border border-line bg-white px-5 py-3 text-sm font-black text-ink">Link Informasi</a>@endif
                    </div>
                </div>
            </div>

            @if ($perumahan->images->count() > 1)
                <section class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit">
                    <h2 class="text-xl font-black">Galeri Perumahan</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($perumahan->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="h-56 w-full rounded-2xl object-cover" alt="{{ $perumahan->nama }}">
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit">
                <h2 class="text-xl font-black">Peta Lokasi</h2>
                <div id="detail-map" class="detail-map mt-5 rounded-[1.5rem]"></div>
            </section>
        </main>
        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit">
                <h2 class="text-xl font-black">Informasi Lengkap</h2>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div><dt class="font-black text-slate-400">Status</dt><dd class="mt-1 font-bold">{{ $statusLabel }}</dd></div>
                    <div><dt class="font-black text-slate-400">Jenis Perumahan</dt><dd class="mt-1 font-bold">{{ $perumahan->jenis_perumahan ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Jumlah Unit</dt><dd class="mt-1 font-bold">{{ $perumahan->jumlah_unit ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Luas</dt><dd class="mt-1 font-bold">{{ $perumahan->luas ? $perumahan->luas . ' ha' : '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Harga Minimum</dt><dd class="mt-1 font-bold text-blue-600">{{ $rupiah($perumahan->harga_min) }}</dd></div>
                    <div><dt class="font-black text-slate-400">Harga Maksimum</dt><dd class="mt-1 font-bold text-blue-600">{{ $rupiah($perumahan->harga_max) }}</dd></div>
                    <div><dt class="font-black text-slate-400">Alamat</dt><dd class="mt-1 font-bold">{{ $perumahan->alamat ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Kelurahan/Desa</dt><dd class="mt-1 font-bold">{{ $perumahan->kelurahan ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Kecamatan</dt><dd class="mt-1 font-bold">{{ $perumahan->kecamatan ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Kabupaten/Provinsi</dt><dd class="mt-1 font-bold">{{ trim(($perumahan->kabupaten ?: '-') . ', ' . ($perumahan->provinsi ?: '-'), ', ') }}</dd></div>
                    <div><dt class="font-black text-slate-400">Koordinat</dt><dd class="mt-1 font-mono text-xs font-bold">{{ $perumahan->latitude }}, {{ $perumahan->longitude }}</dd></div>
                    <div><dt class="font-black text-slate-400">Telepon</dt><dd class="mt-1 font-bold">{{ $perumahan->telepon ?: '-' }}</dd></div>
                    <div><dt class="font-black text-slate-400">Email</dt><dd class="mt-1 font-bold">{{ $perumahan->email ?: '-' }}</dd></div>
                </dl>
            </section>
            <section class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit">
                <h2 class="text-xl font-black">Risiko Terkait</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($perumahan->risikos as $risiko)
                        <div class="rounded-2xl border border-line bg-soft p-4">
                            <p class="font-black">{{ $risiko->nama_risiko }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-500">{{ $risiko->tipe ?: 'Tipe tidak diisi' }} · {{ ucfirst($risiko->tingkat) }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $risiko->deskripsi ?: 'Deskripsi risiko belum tersedia.' }}</p>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-soft p-4 text-sm font-semibold text-slate-500">Belum ada risiko tercatat untuk perumahan ini.</p>
                    @endforelse
                </div>
            </section>
            @if(!empty($perumahan->fasilitas))
                <section class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit"><h2 class="text-xl font-black">Fasilitas</h2><div class="mt-4 flex flex-wrap gap-2">@foreach($perumahan->fasilitas as $fasilitas)<span class="rounded-full bg-soft px-3 py-1.5 text-xs font-black text-slate-700">{{ $fasilitas }}</span>@endforeach</div></section>
            @endif
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const lat = Number(@json($perumahan->latitude)); const lng = Number(@json($perumahan->longitude)); const name = @json($perumahan->nama); const mapsUrl = @json($mapsUrl);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
    const map = L.map('detail-map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    const icon = L.divIcon({ className: '', html: '<div class="zenit-marker active"></div>', iconSize: [24,24], iconAnchor: [12,12], popupAnchor: [0,-15] });
    L.marker([lat, lng], { icon }).addTo(map).bindPopup(`<div class="space-y-2"><p class="font-black">${name}</p>${mapsUrl ? `<a href="${mapsUrl}" target="_blank" class="block rounded-full bg-blue-600 px-3 py-2 text-center text-xs font-black text-white">Buka Google Maps</a>` : ''}</div>`).openPopup();
    setTimeout(() => map.invalidateSize(), 250);
})();
</script>
@endpush
