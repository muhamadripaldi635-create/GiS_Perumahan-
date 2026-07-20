# GIS Perumahan Zenit - Laravel 13 + MySQL

Target folder bawaan script: C:\Users\mypc\Desktop\Joki\Ripal\Laravel

Project ini sudah diubah menjadi Laravel 13 berbasis Blade, MySQL, Tailwind CDN, dan Leaflet CDN.

## Fitur utama

- Halaman publik tanpa login.
- Peta GIS publik dengan marker titik perumahan.
- Full data perumahan + filter.
- Detail perumahan publik.
- Admin-only dashboard.
- CRUD data perumahan.
- Upload beberapa gambar perumahan.
- Pilih titik koordinat dari peta.
- Search lokasi via OpenStreetMap/Nominatim.
- Paste link/teks koordinat Google Maps untuk mengambil latitude-longitude.

## Akun admin default

Email: admin@gisperumahan.local
Password: Admin@12345

## Jalankan

php artisan serve

Buka:
- Publik: http://localhost:8000
- Admin: http://localhost:8000/admin/login

## Database

.env sudah disiapkan untuk MySQL:

DB_DATABASE=gis_perumahan_zenit
DB_USERNAME=root
DB_PASSWORD=
DB_HOST=127.0.0.1
DB_PORT=3306

Jika migrasi gagal, buat database manual lalu jalankan:

php artisan migrate:fresh --seed
php artisan storage:link

## Catatan Google Maps

Pencarian teks memakai OpenStreetMap/Nominatim agar tidak perlu API key. Untuk Google Maps, paste link yang mengandung koordinat seperti @-6.9,106.9 atau teks "-6.9,106.9".
