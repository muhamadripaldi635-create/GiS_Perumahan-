# Update Zenit GIS Perumahan

Patch ini menambahkan:

1. Modul **Kelola Risiko** admin.
   - Relasi risiko dengan data perumahan.
   - Tipe risiko bisa dipilih dari dropdown atau diketik manual.
   - Tingkat risiko: rendah, sedang, tinggi, kritis.
   - Aksi: edit dan hapus.

2. Modul **Laporan Data Perumahan per Kecamatan**.
   - Navigasi laporan per kecamatan.
   - Filter kecamatan.
   - Ringkasan jumlah perumahan, unit, dan risiko.
   - Export CSV.
   - Print laporan.

3. Detail publik perumahan diperluas.
   - Data utama.
   - Lokasi lengkap.
   - Harga, luas, jumlah unit, developer, status, fasilitas.
   - Peta detail.
   - Risiko terkait perumahan.

4. Form admin perumahan diperkuat.
   - Klik peta mengambil titik.
   - Drag marker mengubah latitude/longitude.
   - Search lokasi via OpenStreetMap/Nominatim.
   - Paste koordinat langsung.
   - Paste URL Google Maps yang mengandung pola koordinat `@lat,lng`, `!3dlat!4dlng`, `q=lat,lng`, `query=lat,lng`, `ll=lat,lng`, atau `center=lat,lng`.

5. Tampilan admin dibuat lebih konsisten, minimalis, dan responsif untuk mobile, tablet, dan desktop.
