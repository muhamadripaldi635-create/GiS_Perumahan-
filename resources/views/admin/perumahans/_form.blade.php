@php
    $points = $allPoints->map(fn ($item) => [
        'id' => $item->id,
        'nama' => $item->nama,
        'lat' => (float) $item->latitude,
        'lng' => (float) $item->longitude,
        'status' => $item->status,
        'kecamatan' => $item->kecamatan,
        'risikos_count' => $item->risikos_count ?? 0,
    ])->values();
    $recommendationMode = $recommendationMode ?? false;
@endphp
<form id="{{ $recommendationMode ? 'recommendation-form' : 'perumahan-form' }}" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid gap-8 xl:grid-cols-[1.9fr_1fr]">
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <div class="space-y-7">
        <section class="card rounded-[2rem] p-6">
            <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Identitas Perumahan</p>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="md:col-span-2"><span class="text-sm font-black">Nama Perumahan *</span><input name="nama" required value="{{ old('nama', $perumahan->nama) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Jenis Perumahan</span><input name="jenis_perumahan" value="{{ old('jenis_perumahan', $perumahan->jenis_perumahan) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Status *</span><select name="status" required class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">@foreach(['tersedia'=>'Tersedia','proses'=>'Proses','terjual'=>'Terjual','tidak_aktif'=>'Tidak Aktif'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $perumahan->status) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label class="flex items-center gap-3"><input type="checkbox" name="is_recommended" value="1" @checked(old('is_recommended', $perumahan->is_recommended)) class="h-4 w-4 rounded border-line text-blue-600 focus:ring-blue-600"><span class="text-sm font-black">Rekomendasi</span></label>
                <label><span class="text-sm font-black">Harga Minimum</span><input name="harga_min" type="number" min="0" value="{{ old('harga_min', $perumahan->harga_min) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Harga Maksimum</span><input name="harga_max" type="number" min="0" value="{{ old('harga_max', $perumahan->harga_max) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Luas</span><input name="luas" type="number" step="0.01" min="0" value="{{ old('luas', $perumahan->luas) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Jumlah Unit</span><input name="jumlah_unit" type="number" min="0" value="{{ old('jumlah_unit', $perumahan->jumlah_unit) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="md:col-span-2"><span class="text-sm font-black">Deskripsi</span><textarea name="deskripsi" rows="5" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('deskripsi', $perumahan->deskripsi) }}</textarea></label>
                <label class="md:col-span-2"><span class="text-sm font-black">Fasilitas</span><textarea name="fasilitas" rows="3" placeholder="Pisahkan dengan koma atau baris baru" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('fasilitas', is_array($perumahan->fasilitas) ? implode(', ', $perumahan->fasilitas) : '') }}</textarea></label>
            </div>
        </section>
        <section class="card rounded-[2rem] p-6">
            <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Kontak & Link Akses</p>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label><span class="text-sm font-black">Nomor Telepon</span><input name="telepon" value="{{ old('telepon', $perumahan->telepon) }}" placeholder="Contoh: 0812xxxx" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Email</span><input name="email" type="email" value="{{ old('email', $perumahan->email) }}" placeholder="kontak@email.com" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Link Website/Informasi</span><input name="website_url" type="url" value="{{ old('website_url', $perumahan->website_url) }}" placeholder="https://..." class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Link Google Maps</span><input id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url', $perumahan->google_maps_url) }}" placeholder="Otomatis dari titik / paste link Google Maps" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
            </div>
        </section>
        <section class="card rounded-[2rem] p-6">
            <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Alamat & Koordinat</p>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="md:col-span-2"><span class="text-sm font-black">Alamat</span><input id="alamat" name="alamat" value="{{ old('alamat', $perumahan->alamat) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Kelurahan/Desa</span><input name="kelurahan" value="{{ old('kelurahan', $perumahan->kelurahan) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Kecamatan</span><input name="kecamatan" value="{{ old('kecamatan', $perumahan->kecamatan) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Kabupaten</span><input name="kabupaten" value="{{ old('kabupaten', $perumahan->kabupaten) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Provinsi</span><input name="provinsi" value="{{ old('provinsi', $perumahan->provinsi) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Latitude *</span><input id="latitude" name="latitude" type="number" step="any" required value="{{ old('latitude', $perumahan->latitude) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 font-mono text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label><span class="text-sm font-black">Longitude *</span><input id="longitude" name="longitude" type="number" step="any" required value="{{ old('longitude', $perumahan->longitude) }}" class="mt-2 w-full rounded-2xl border-line px-4 py-3 font-mono text-sm focus:border-blue-600 focus:ring-blue-600"></label>
            </div>
        </section>
        <section class="card rounded-[2rem] p-6">
            <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Gambar Perumahan</p>
            <p class="mt-2 text-sm text-slate-500">Upload beberapa gambar sekaligus. Format JPG, PNG, WEBP. Maksimal 5 MB per gambar.</p>
            <input id="gambar-input" name="gambar[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="mt-5 w-full rounded-2xl border border-dashed border-line bg-soft px-4 py-4 text-sm">
            <div id="image-preview" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
            @if ($perumahan->exists && $perumahan->images->count())
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">@foreach ($perumahan->images as $image)<label class="overflow-hidden rounded-2xl border border-line bg-white"><img src="{{ asset('storage/' . $image->path) }}" class="h-40 w-full object-cover" alt="{{ $perumahan->nama }}"><span class="flex items-center gap-2 p-3 text-sm font-black text-rose-600"><input type="checkbox" name="hapus_gambar[]" value="{{ $image->id }}"> Hapus gambar ini</span></label>@endforeach</div>
            @endif
        </section>
    </div>
    <aside class="space-y-7">
        @if ($recommendationMode)
            <section class="card rounded-[2rem] p-5">
                <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Status Rekomendasi</p>
                <div class="mt-5 space-y-3 text-sm text-slate-600">
                    <p class="font-black text-slate-900">Rekomendasi akan tampil di daftar unggulan jika checkbox rekomendasi dicentang saat disimpan.</p>
                    <ul class="space-y-2 text-sm leading-6 text-slate-600">
                        <li>• Pastikan status diatur ke <strong>tersedia</strong>.</li>
                        <li>• Tambahkan minimal 3 gambar perumahan.</li>
                        <li>• Validasi koordinat agar peta akurat.</li>
                    </ul>
                </div>
            </section>
        @endif
        <section class="card rounded-[2rem] p-5">
            <p class="text-xs font-black uppercase tracking-[.25em] text-blue-600">Pilih Titik di Peta</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">Klik peta, drag marker, search lokasi, paste koordinat, atau paste link Google Maps.</p>
            <div class="mt-4 flex gap-2"><input id="map-search" class="min-w-0 flex-1 rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Cari alamat / paste link Google Maps"><button type="button" id="map-search-btn" class="rounded-2xl btn-blue px-4 py-3 text-sm font-black">Cari</button></div>
            <div id="search-results" class="mt-3 space-y-2"></div>
            <div id="admin-map" class="admin-map mt-4 rounded-[1.5rem]"></div>
            <p class="mt-3 text-xs leading-5 text-slate-400">Catatan: search teks memakai OpenStreetMap/Nominatim. Link Google Maps pendek kadang tidak membawa koordinat; gunakan link panjang atau paste koordinat langsung.</p>
        </section>
        <section class="card rounded-[2rem] p-5">
            <button type="submit" class="w-full rounded-full px-5 py-3 text-sm font-black text-white shadow-lg transition hover:bg-blue-900" style="background-color:#072b57;">Simpan Data</button>
            <a href="{{ route('admin.perumahans.index') }}" class="mt-3 block w-full rounded-full border border-slate-200 bg-white px-5 py-3 text-center text-sm font-black text-slate-700 transition hover:bg-slate-100">Batal</a>
        </section>
    </aside>
</form>
<script id="existing-points" type="application/json">{!! json_encode($points, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@push('scripts')
<script>
(function () {
    const latInput = document.getElementById('latitude'), lngInput = document.getElementById('longitude'), alamatInput = document.getElementById('alamat'), searchInput = document.getElementById('map-search'), resultsBox = document.getElementById('search-results'), mapsInput = document.getElementById('google_maps_url');
    const existing = JSON.parse(document.getElementById('existing-points').textContent || '[]').filter(p => Number.isFinite(Number(p.lat)) && Number.isFinite(Number(p.lng)) && Number(p.lat) !== 0 && Number(p.lng) !== 0);
    const initialLat = Number(latInput.value) || -6.9181, initialLng = Number(lngInput.value) || 106.9272;
    const map = L.map('admin-map').setView([initialLat, initialLng], latInput.value && lngInput.value ? 15 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    const existingIcon = L.divIcon({ className: '', html: '<div class="existing-marker"></div>', iconSize:[14,14], iconAnchor:[7,7] });
    existing.forEach(point => L.marker([Number(point.lat), Number(point.lng)], { icon: existingIcon }).addTo(map).bindPopup(`<div class="space-y-2"><p class="font-black">${escapeHtml(point.nama)}</p><p class="text-xs text-slate-500">${escapeHtml(point.kecamatan || '-')} · Risiko ${point.risikos_count || 0}</p><button type="button" class="use-existing rounded-full bg-blue-600 px-3 py-2 text-xs font-black text-white" data-lat="${point.lat}" data-lng="${point.lng}">Pakai titik ini</button></div>`));
    const pickIcon = L.divIcon({ className: '', html: '<div class="pick-marker"></div>', iconSize:[26,26], iconAnchor:[13,13], popupAnchor:[0,-15] });
    let marker = L.marker([initialLat, initialLng], { icon: pickIcon, draggable: true }).addTo(map);
    marker.on('dragend', event => setPoint(event.target.getLatLng().lat, event.target.getLatLng().lng, true));
    map.on('click', event => setPoint(event.latlng.lat, event.latlng.lng, true));
    map.on('popupopen', () => document.querySelectorAll('.use-existing').forEach(btn => btn.addEventListener('click', () => setPoint(btn.dataset.lat, btn.dataset.lng, false))));
    mapsInput.addEventListener('input', () => { mapsInput.dataset.auto = '0'; });
    function mapsFromCoords(lat, lng) { return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`; }
    function setPoint(lat, lng, reverse = false, originalUrl = '') { lat = Number(lat); lng = Number(lng); if (!Number.isFinite(lat) || !Number.isFinite(lng)) return; latInput.value = lat.toFixed(7); lngInput.value = lng.toFixed(7); marker.setLatLng([lat,lng]); map.setView([lat,lng], Math.max(map.getZoom(), 15)); if (originalUrl && /^https?:\/\//i.test(originalUrl)) { mapsInput.value = originalUrl; mapsInput.dataset.auto = '0'; } else if (!mapsInput.value || mapsInput.dataset.auto === '1' || mapsInput.dataset.auto === undefined) { mapsInput.value = mapsFromCoords(lat.toFixed(7), lng.toFixed(7)); mapsInput.dataset.auto = '1'; } if (reverse) reverseAddress(lat,lng); }
    function extractCoords(text) { const value = String(text || ''); const patterns = [/@(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/, /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/, /[?&](?:q|query|ll|center)=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/, /(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)/]; for (const pattern of patterns) { const match = value.match(pattern); if (match) return [Number(match[1]), Number(match[2])]; } return null; }
    async function reverseAddress(lat,lng) { try { const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`); const json = await response.json(); if (json?.display_name && !alamatInput.value) alamatInput.value = json.display_name; } catch (error) {} }
    async function searchLocation() {
        const q = searchInput.value.trim();
        resultsBox.innerHTML = '';
        if (!q) return;

        const coords = extractCoords(q);
        if (coords) {
            setPoint(coords[0], coords[1], true, /^https?:\/\//i.test(q) ? q : '');
            resultsBox.innerHTML = '<div class="rounded-2xl bg-blue-50 px-4 py-3 text-xs font-black text-blue-700">Koordinat berhasil dipakai.</div>';
            return;
        }

        resultsBox.innerHTML = '<div class="rounded-2xl bg-soft px-4 py-3 text-xs font-black text-slate-500">Mencari lokasi...</div>';
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id&q=${encodeURIComponent(q)}`);
            const results = await response.json();
            if (!Array.isArray(results) || !results.length) {
                resultsBox.innerHTML = '<div class="rounded-2xl bg-rose-50 px-4 py-3 text-xs font-black text-rose-600">Lokasi tidak ditemukan.</div>';
                return;
            }

            resultsBox.innerHTML = results.map(item => `<button type="button" class="search-pick block w-full rounded-2xl border border-line bg-white px-4 py-3 text-left text-xs font-semibold text-slate-600 hover:border-blue-600" data-lat="${item.lat}" data-lng="${item.lon}" data-name="${escapeHtml(item.display_name)}">${escapeHtml(item.display_name)}</button>`).join('');
            document.querySelectorAll('.search-pick').forEach(btn => btn.addEventListener('click', () => {
                alamatInput.value = btn.dataset.name || alamatInput.value;
                setPoint(btn.dataset.lat, btn.dataset.lng, false);
                resultsBox.innerHTML = '';
            }));
        } catch (error) {
            resultsBox.innerHTML = '<div class="rounded-2xl bg-rose-50 px-4 py-3 text-xs font-black text-rose-600">Gagal mencari lokasi. Cek koneksi internet.</div>';
        }
    }

    function previewImages(files) {
        const preview = document.getElementById('image-preview');
        if (!preview) return;
        preview.innerHTML = '';
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = event => {
                const wrapper = document.createElement('div');
                wrapper.className = 'overflow-hidden rounded-2xl border border-line bg-white';
                wrapper.innerHTML = `<img src="${event.target.result}" class="h-40 w-full object-cover" alt="Preview gambar">`;
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    const imageInput = document.getElementById('gambar-input');
    if (imageInput) {
        imageInput.addEventListener('change', event => previewImages(event.target.files));
    }

    function escapeHtml(value) { return String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c])); }
    document.getElementById('map-search-btn').addEventListener('click', searchLocation);
    searchInput.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); searchLocation(); } });
    latInput.addEventListener('change', () => setPoint(latInput.value, lngInput.value, false));
    lngInput.addEventListener('change', () => setPoint(latInput.value, lngInput.value, false));
    setPoint(initialLat, initialLng, false);
    setTimeout(() => map.invalidateSize(), 250);
})();
</script>
@endpush

@push('scripts')
<script>
(function () {
    const imageInput = document.getElementById('gambar-input');
    const previewContainer = document.getElementById('image-preview');
    if (!imageInput || !previewContainer) return;

    function renderPreview(files) {
        previewContainer.innerHTML = '';
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = event => {
                const item = document.createElement('div');
                item.className = 'overflow-hidden rounded-2xl border border-line bg-white';
                item.innerHTML = `<img src="${event.target.result}" class="h-40 w-full object-cover" alt="Preview gambar">`;
                previewContainer.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    imageInput.addEventListener('change', event => renderPreview(event.target.files));
})();
</script>
@endpush
