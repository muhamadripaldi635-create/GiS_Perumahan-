# Update Risiko, Popup Marker, Kontak, dan UI Zenit Minimalis

Perubahan utama:

1. Popup marker publik kini menampilkan nama perumahan, kecamatan, status, harga, kontak, alamat, ringkasan risiko, tombol detail, dan tombol Google Maps.
2. Field Developer di form Tambah/Edit Perumahan dihapus dari UI dan validasi input admin.
3. Perumahan kini punya field kontak: telepon, email, website/link informasi, dan link Google Maps.
4. Link Google Maps otomatis dibuat dari latitude/longitude saat titik dipilih dari peta, atau bisa diisi dari paste link Google Maps.
5. Modul Kelola Risiko dibuat/ditimpa ulang agar konsisten: perumahan, nama risiko, tipe dropdown/manual, tingkat, deskripsi, mitigasi, status tindak lanjut, edit, hapus.
6. Modul Laporan per Kecamatan dibuat/ditimpa ulang: filter kecamatan, print, export CSV.
7. Tampilan publik dan admin distandarkan ke palet putih, hitam, dan biru untuk tombol/action.
8. Tampilan dibuat responsif untuk mobile, tablet, dan desktop memakai Tailwind CDN.
9. Peta tetap memakai Leaflet CDN, bukan dependency npm.

Setelah menjalankan script, jalankan:

```bash
php artisan migrate
php artisan storage:link
php artisan optimize:clear
php artisan serve
```
