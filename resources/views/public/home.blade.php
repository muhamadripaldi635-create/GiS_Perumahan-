@extends('layouts.public')

@section('title', 'GIS Perumahan- Peta Publik')

@section('content')
@php
    $mapData = $perumahans->map(function ($item) {
        $cover = $item->images->first();
        $risikoRingkas = $item->risikos->take(3)->map(fn ($r) => trim($r->nama_risiko . ' (' . ucfirst($r->tingkat) . ')'))->values();
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'nama' => $item->nama,
            'deskripsi' => $item->deskripsi,
            'alamat' => $item->alamat,
            'kelurahan' => $item->kelurahan,
            'kecamatan' => $item->kecamatan,
            'kabupaten' => $item->kabupaten,
            'provinsi' => $item->provinsi,
            'latitude' => (float) $item->latitude,
            'longitude' => (float) $item->longitude,
            'harga_min' => $item->harga_min,
            'harga_max' => $item->harga_max,
            'status' => $item->status,
            'jenis_perumahan' => $item->jenis_perumahan,
            'fasilitas' => $item->fasilitas ?? [],
            'telepon' => $item->telepon,
            'email' => $item->email,
            'website_url' => $item->website_url,
            'maps_url' => $item->googleMapsDirectUrl(),
            'risiko_count' => $item->risikos->count(),
            'risiko_ringkas' => $risikoRingkas,
            'risiko_tertinggi' => optional($item->risikos->sortByDesc(fn ($r) => ['rendah' => 1, 'sedang' => 2, 'tinggi' => 3, 'kritis' => 4][$r->tingkat] ?? 0)->first())->tingkat,
            'cover' => $cover ? asset('storage/' . $cover->path) : null,
            'url' => route('perumahan.show', $item),
        ];
    })->values();
@endphp

<section
   class="relative min-h-screen bg-cover bg-center bg-no-repeat"
style="background-image: url('/bgg.jpg');">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Content -->
    <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-8">

        <div>
            <p class="text-xs font-black uppercase tracking-[.32em] text-white/70">
            </p>

            <h1 class="mt-5 max-w-4xl text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                Sistem Informasi Geografis untuk Menemukan Perumahan Terbaik di Kabupaten Sukabumi.
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-white/80">
                Lihat sebaran titik perumahan, risiko terkait, kontak, email,
                dan akses langsung ke Google Maps tanpa login.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#peta"
                    class="rounded-full bg-blue-600 px-6 py-3 text-sm font-bold text-white hover:bg-blue-700">
                    Buka Peta GIS
                </a>

                <a href="#data"
                    class="rounded-full border border-white bg-white/20 px-6 py-3 text-sm font-bold text-white hover:bg-white hover:text-black">
                    Lihat Data
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">

            <div class="rounded-3xl bg-white/90 p-6 shadow-xl">
                <p class="text-sm font-semibold text-gray-500">
                    Total Perumahan
                </p>
                <p class="mt-2 text-4xl font-black">
                    {{ $perumahans->count() }}
                </p>
            </div>

            <div class="rounded-3xl bg-white/90 p-6 shadow-xl">
                <p class="text-sm font-semibold text-gray-500">
                    Kecamatan
                </p>
                <p class="mt-2 text-4xl font-black">
                    {{ $kecamatans->count() }}
                </p>
            </div>

            <div class="rounded-3xl bg-white/90 p-6 shadow-xl">
                <p class="text-sm font-semibold text-gray-500">
                    Risiko Tercatat
                </p>
                <p class="mt-2 text-4xl font-black">
                    {{ $perumahans->sum(fn($p) => $p->risikos->count()) }}
                </p>
            </div>

        </div>

    </div>

</section>

<section id="rekomendasi" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Rekomendasi</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">Lokasi Perumahan yang Di Rekomendasikan</h2>
        </div>
        <div class="rounded-full border border-line bg-white px-4 py-2 text-sm font-black text-slate-600">{{ $recommendedPerumahans->count() }} rekomendasi</div>
    </div>

    @if($recommendedPerumahans->isEmpty())
        <div class="rounded-[2rem] border border-line bg-white p-8 text-center text-sm font-bold text-slate-500">Belum ada rekomendasi perumahan saat ini.</div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach($recommendedPerumahans as $item)
                @php
                    $coverImage = $item->images->first();
                    $coverUrl = $coverImage ? asset('storage/' . $coverImage->path) : null;
                @endphp
                <article class="overflow-hidden rounded-[2rem] border border-line bg-white shadow-zenit">
                    <a href="{{ route('perumahan.show', $item) }}" class="block">
                        <div class="relative h-56 bg-soft">
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $item->nama }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full place-items-center p-4 text-xs font-black uppercase tracking-[.22em] text-slate-400">Gambar belum tersedia</div>
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-[11px] font-black uppercase tracking-[.22em] text-blue-600">{{ $item->kecamatan ?? '-' }}</p>
                            <h3 class="mt-3 text-xl font-black text-ink">{{ $item->nama }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-500">{{ $item->deskripsi ? \Illuminate\Support\Str::limit($item->deskripsi, 90) : 'Deskripsi belum tersedia.' }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if(is_array($item->risiko_ringkas) && count($item->risiko_ringkas))
                                    @foreach($item->risiko_ringkas as $risiko)
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ $risiko }}</span>
                                    @endforeach
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">Belum ada risiko tercatat</span>
                                @endif
                            </div>
                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Harga</p>
                                    <p class="text-sm font-black text-ink">
                                        @if($item->harga_min && $item->harga_max)
                                            Rp {{ number_format($item->harga_min, 0, ',', '.') }} - Rp {{ number_format($item->harga_max, 0, ',', '.') }}
                                        @elseif($item->harga_min)
                                            Rp {{ number_format($item->harga_min, 0, ',', '.') }}
                                        @elseif($item->harga_max)
                                            Rp {{ number_format($item->harga_max, 0, ',', '.') }}
                                        @else
                                            Belum tersedia
                                        @endif
                                    </p>
                                </div>
                                <span class="rounded-full bg-blue-600 px-4 py-2 text-xs font-black text-white">Detail</span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    @endif
</section>

<section id="peta" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div><p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Peta GIS Publik</p><h2 class="mt-2 text-3xl font-black tracking-tight">Titik Lokasi Perumahan</h2></div>
        <div class="rounded-full border border-line bg-white px-4 py-2 text-sm font-black text-slate-600"><span id="visible-count">{{ $perumahans->count() }}</span> titik tampil</div>
    </div>
    <div class="grid gap-6 lg:grid-cols-[1fr_390px]">
        <div class="overflow-hidden rounded-[2rem] border border-line bg-white p-3 shadow-zenit"><div id="public-map" class="map-shell rounded-[1.5rem]"></div></div>
        <aside class="rounded-[2rem] border border-line bg-white p-6 shadow-zenit">
            <p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Detail Terpilih</p>
            <div id="selected-detail" class="mt-5 text-sm text-slate-600"><div class="rounded-2xl bg-soft p-6 text-center font-semibold text-slate-500">Klik marker atau kartu data untuk menampilkan detail ringkas.</div></div>
        </aside>
    </div>
</section>

<section id="data" class="border-y border-line bg-soft py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-7 flex flex-col justify-between gap-5 xl:flex-row xl:items-end">
            <div><p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Full Data</p><h2 class="mt-2 text-3xl font-black tracking-tight">Daftar Perumahan</h2></div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <input id="filter-keyword" class="rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Cari nama, alamat, kontak, jenis...">
                <select id="filter-kecamatan" class="rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"><option value="">Semua Kecamatan</option>@foreach ($kecamatans as $kecamatan)<option value="{{ $kecamatan }}">{{ $kecamatan }}</option>@endforeach</select>
                <select id="filter-risk-type" class="rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"><option value="">Semua Jenis Risiko</option>@foreach ($riskTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
                <select id="filter-status" class="rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"><option value="">Semua Status</option><option value="tersedia">Tersedia</option><option value="proses">Proses</option><option value="terjual">Terjual</option><option value="tidak_aktif">Tidak Aktif</option></select>
            </div>
        </div>
        <div id="cards-grid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"></div>
    </div>
</section>

<section id="tentang" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="rounded-[2rem] bg-ink p-8 text-white shadow-zenit md:p-12">
        <p class="text-xs font-black uppercase tracking-[.3em] text-blue-300">Tentang Sistem</p>
        <h2 class="mt-3 text-3xl font-black tracking-tight">Publik melihat data. Admin mengelola data.</h2>
        <p class="mt-4 max-w-3xl leading-8 text-slate-300">Halaman publik memuat peta, detail, risiko, kontak, dan link Google Maps. Dashboard admin memuat CRUD perumahan, galeri gambar, risiko, serta laporan per kecamatan.</p>
    </div>
</section>

<script id="perumahan-map-data" type="application/json">{!! json_encode($mapData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection

@push('scripts')
<script>
(function () {
    const raw = document.getElementById('perumahan-map-data').textContent || '[]';
    const allData = JSON.parse(raw).filter(item => Number.isFinite(Number(item.latitude)) && Number.isFinite(Number(item.longitude)) && Number(item.latitude) !== 0 && Number(item.longitude) !== 0);
    const state = { keyword: '', kecamatan: '', riskType: '', status: '', selected: allData[0] || null };
    const map = L.map('public-map', { zoomControl: true, scrollWheelZoom: true }).setView([-6.9181, 106.9272], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    const layer = L.layerGroup().addTo(map);
    const markers = new Map();

    const rupiah = value => value ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value) : 'Belum tersedia';
    const price = item => item.harga_min && item.harga_max ? `${rupiah(item.harga_min)} - ${rupiah(item.harga_max)}` : rupiah(item.harga_min || item.harga_max);
    const statusText = s => ({ tersedia:'Tersedia', proses:'Proses', terjual:'Terjual', tidak_aktif:'Tidak Aktif' }[s] || '-');
    const riskTone = s => ({ rendah:'bg-slate-100 text-slate-700', sedang:'bg-blue-50 text-blue-700', tinggi:'bg-orange-50 text-orange-700', kritis:'bg-rose-50 text-rose-700' }[s] || 'bg-slate-100 text-slate-600');
    const esc = value => String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;',"\"":'&quot;'}[c]));
    const valid = item => Number(item.latitude) >= -90 && Number(item.latitude) <= 90 && Number(item.longitude) >= -180 && Number(item.longitude) <= 180;
    const contactText = item => item.telepon || item.email || 'Kontak belum tersedia';

    function currentData() {
        const q = state.keyword.toLowerCase().trim();
        return allData.filter(item => {
            const haystack = [item.nama, item.alamat, item.kelurahan, item.kecamatan, item.telepon, item.email, item.jenis_perumahan].join(' ').toLowerCase();
            const matchesRiskType = !state.riskType || (Array.isArray(item.risiko_ringkas) && item.risiko_ringkas.some(r => String(r).toLowerCase().includes(state.riskType)));
            return (!q || haystack.includes(q)) && (!state.kecamatan || item.kecamatan === state.kecamatan) && matchesRiskType && (!state.status || item.status === state.status);
        });
    }
    function makeIcon(active = false) { return L.divIcon({ className: '', html: `<div class="zenit-marker ${active ? 'active' : ''}"></div>`, iconSize: [24,24], iconAnchor: [12,12], popupAnchor: [0,-15] }); }
    function renderRisks(item) {
        const risks = Array.isArray(item.risiko_ringkas) && item.risiko_ringkas.length ? item.risiko_ringkas : ['Belum ada risiko tercatat'];
        return risks.map(r => `<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">${esc(r)}</span>`).join(' ');
    }
    function renderMarkers(fit = false) {
        const data = currentData().filter(valid); layer.clearLayers(); markers.clear(); const bounds = [];
        data.forEach(item => {
            const marker = L.marker([Number(item.latitude), Number(item.longitude)], { icon: makeIcon(state.selected && state.selected.id === item.id) });
            marker.bindPopup(`
                <div class="space-y-3">
                    <div><p class="text-base font-black text-ink">${esc(item.nama)}</p><p class="text-xs font-semibold text-slate-500">${esc(item.kecamatan || '-')} · ${esc(statusText(item.status))}</p></div>
                    <div class="rounded-2xl bg-soft p-3"><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Risiko</p><div class="mt-2 flex flex-wrap gap-1.5">${renderRisks(item)}</div></div>
                    <div class="grid gap-1 text-xs text-slate-600"><p><b>Harga:</b> ${esc(price(item))}</p><p><b>Kontak:</b> ${esc(contactText(item))}</p><p><b>Alamat:</b> ${esc(item.alamat || '-')}</p></div>
                    <div class="grid grid-cols-2 gap-2"><a class="rounded-full bg-blue-600 px-3 py-2 text-center text-xs font-black text-white" href="${esc(item.url)}">Detail</a>${item.maps_url ? `<a target="_blank" class="rounded-full border border-line px-3 py-2 text-center text-xs font-black text-ink" href="${esc(item.maps_url)}">Google Maps</a>` : `<span class="rounded-full border border-line px-3 py-2 text-center text-xs font-black text-slate-400">Maps -</span>`}</div>
                </div>`);
            marker.on('click', () => selectItem(item, false)); marker.addTo(layer); markers.set(item.id, marker); bounds.push([Number(item.latitude), Number(item.longitude)]);
        });
        document.getElementById('visible-count').textContent = data.length;
        if (fit && bounds.length) map.fitBounds(bounds, { padding: [42,42], maxZoom: 14 });
    }
    function renderCards() {
        const container = document.getElementById('cards-grid'); const data = currentData();
        if (!data.length) { container.innerHTML = '<div class="col-span-full rounded-[2rem] border border-line bg-white py-16 text-center text-sm font-bold text-slate-500">Data tidak ditemukan.</div>'; return; }
        container.innerHTML = data.map(item => `
            <article class="card-hover overflow-hidden rounded-[2rem] border border-line bg-white">
                <button type="button" data-id="${item.id}" class="select-card block w-full text-left">
                    <div class="relative h-52 bg-soft">${item.cover ? `<img src="${esc(item.cover)}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" alt="${esc(item.nama)}">` : `<div class="grid h-full place-items-center text-xs font-black uppercase tracking-wider text-slate-400">Gambar belum tersedia</div>`}<span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-black text-ink">${esc(statusText(item.status))}</span></div>
                    <div class="p-6"><p class="text-[11px] font-black uppercase tracking-[.22em] text-blue-600">${esc(item.kecamatan || '-')}</p><h3 class="mt-2 text-lg font-black leading-snug">${esc(item.nama)}</h3><p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">${esc(item.alamat || '-')}</p><div class="mt-4 flex flex-wrap gap-2">${renderRisks(item)}</div><div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4"><div><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Harga</p><p class="text-sm font-black">${esc(price(item))}</p></div><a href="${esc(item.url)}" class="rounded-full bg-blue-600 px-4 py-2 text-xs font-black text-white">Detail</a></div></div>
                </button>
            </article>`).join('');
        container.querySelectorAll('.select-card').forEach(btn => btn.addEventListener('click', event => { if (event.target.closest('a')) return; const item = allData.find(row => String(row.id) === String(btn.dataset.id)); if (item) selectItem(item, true); }));
    }
    function renderDetail() {
        const box = document.getElementById('selected-detail'); const item = state.selected;
        if (!item) { box.innerHTML = '<div class="rounded-2xl bg-soft p-6 text-center font-semibold text-slate-500">Belum ada data dipilih.</div>'; return; }
        box.innerHTML = `
            <div class="overflow-hidden rounded-2xl bg-soft">${item.cover ? `<img src="${esc(item.cover)}" class="h-48 w-full object-cover" alt="${esc(item.nama)}">` : `<div class="grid h-48 place-items-center text-xs font-black text-slate-400">Gambar belum tersedia</div>`}</div>
            <div class="mt-5"><span class="inline-flex rounded-full ${riskTone(item.risiko_tertinggi)} px-3 py-1 text-[11px] font-black uppercase">Risiko: ${item.risiko_count || 0}</span><h3 class="mt-3 text-2xl font-black tracking-tight">${esc(item.nama)}</h3><p class="mt-3 rounded-2xl bg-soft p-4 text-sm leading-7 text-slate-600">${esc(item.deskripsi || 'Deskripsi belum tersedia.')}</p></div>
            <dl class="mt-5 grid grid-cols-2 gap-3 text-xs"><div class="col-span-2"><dt class="font-black uppercase tracking-wider text-slate-400">Alamat</dt><dd class="mt-1 font-semibold text-slate-700">${esc(item.alamat || '-')}</dd></div><div><dt class="font-black uppercase tracking-wider text-slate-400">Kecamatan</dt><dd class="mt-1 font-semibold text-slate-700">${esc(item.kecamatan || '-')}</dd></div><div><dt class="font-black uppercase tracking-wider text-slate-400">Harga</dt><dd class="mt-1 font-black text-blue-600">${esc(price(item))}</dd></div><div><dt class="font-black uppercase tracking-wider text-slate-400">Telepon</dt><dd class="mt-1 font-semibold text-slate-700">${esc(item.telepon || '-')}</dd></div><div><dt class="font-black uppercase tracking-wider text-slate-400">Email</dt><dd class="mt-1 font-semibold text-slate-700">${esc(item.email || '-')}</dd></div></dl>
            <div class="mt-5 rounded-2xl bg-soft p-4"><p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Risiko Terkait</p><div class="mt-2 flex flex-wrap gap-2">${renderRisks(item)}</div></div>
            <div class="mt-5 grid grid-cols-2 gap-2"><a href="${esc(item.url)}" class="rounded-full btn-blue px-4 py-3 text-center text-xs font-black">Detail Lengkap</a>${item.maps_url ? `<a href="${esc(item.maps_url)}" target="_blank" class="rounded-full border border-line bg-white px-4 py-3 text-center text-xs font-black text-ink">Google Maps</a>` : `<span class="rounded-full border border-line bg-white px-4 py-3 text-center text-xs font-black text-slate-400">Maps -</span>`}</div>`;
    }
    function selectItem(item, focusMap) { state.selected = item; renderDetail(); renderMarkers(false); if (focusMap && valid(item)) { map.setView([Number(item.latitude), Number(item.longitude)], Math.max(map.getZoom(), 15)); const marker = markers.get(item.id); if (marker) marker.openPopup(); } }
    function refresh(fit=false) { renderMarkers(fit); renderCards(); renderDetail(); }
    document.getElementById('filter-keyword').addEventListener('input', e => { state.keyword = e.target.value; refresh(true); });
    document.getElementById('filter-kecamatan').addEventListener('change', e => { state.kecamatan = e.target.value; refresh(true); });
    document.getElementById('filter-risk-type').addEventListener('change', e => { state.riskType = e.target.value; refresh(true); });
    document.getElementById('filter-status').addEventListener('change', e => { state.status = e.target.value; refresh(true); });
    refresh(true); setTimeout(() => map.invalidateSize(), 250);
})();
</script>
@endpush
