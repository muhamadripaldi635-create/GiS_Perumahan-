<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GIS Perumahan')</title>
    <meta name="description" content="Sistem Informasi Geografis Perumahan berbasis Laravel, MySQL, Tailwind CDN, dan Leaflet CDN.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <script src="https://cdn.tailwindcss.com?plugins=forms,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { ink: '#050505', soft: '#f6f7f9', line: '#e7e9ee', blue: '#2563eb' },
                    boxShadow: { zenit: '0 24px 80px rgba(15,23,42,.08)' },
                    keyframes: {
                        fadeUp: { '0%': { opacity: 0, transform: 'translateY(14px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        softPulse: { '0%,100%': { transform: 'scale(1)', opacity: 1 }, '50%': { transform: 'scale(1.05)', opacity: .85 } }
                    },
                    animation: { fadeUp: 'fadeUp .55s ease both', softPulse: 'softPulse 2.4s ease-in-out infinite' }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { background:#fff; color:#050505; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 600,'GRAD' 0,'opsz' 24; vertical-align:middle; }
        .map-shell { height: 620px; min-height: 420px; width: 100%; background:#f6f7f9; }
        .detail-map { height: 420px; min-height: 320px; width: 100%; background:#f6f7f9; }
        .leaflet-container { font-family: Inter, sans-serif; }
        .leaflet-popup-content-wrapper { border-radius: 20px; box-shadow: 0 24px 70px rgba(15,23,42,.18); border: 1px solid rgba(226,232,240,.9); }
        .leaflet-popup-content { margin: 14px; min-width: 260px; }
        .leaflet-popup-tip { box-shadow: none; }
        .zenit-marker { width: 22px; height: 22px; border-radius: 999px; background:#2563eb; border:4px solid white; box-shadow:0 10px 28px rgba(37,99,235,.36),0 0 0 7px rgba(37,99,235,.13); }
        .zenit-marker.active { background:#050505; box-shadow:0 12px 34px rgba(5,5,5,.35),0 0 0 8px rgba(37,99,235,.18); transform: scale(1.12); }
        .btn-blue { background:#2563eb; color:#fff; transition:all .2s ease; }
        .btn-blue:hover { background:#1d4ed8; transform: translateY(-1px); box-shadow:0 16px 34px rgba(37,99,235,.22); }
        .btn-secondary { background:#fff; border:1px solid #dfe3ea; color:#050505; transition:all .2s ease; }
        .btn-secondary:hover { background:#f8fafc; transform: translateY(-1px); box-shadow:0 12px 28px rgba(15,23,42,.08); }
        .btn-black { background:#050505; color:#fff; transition:all .2s ease; }
        .btn-black:hover { background:#111827; transform: translateY(-1px); }
        .card-hover { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow:0 24px 80px rgba(15,23,42,.09); border-color:#d7dce5; }
        @media (max-width: 768px) { .map-shell { height: 460px; } }
    </style>
    @stack('head')
</head>
<body class="font-sans antialiased bg-white text-ink">
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-line bg-white/88 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-ink text-sm font-black text-white">Z</span>
                <span>
                    <span class="block text-sm font-black tracking-tight">GIS Perumahan</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[.24em] text-slate-400">Sistem GIS</span>
                </span>
            </a>
            <div class="hidden items-center gap-7 text-sm font-bold text-slate-600 md:flex">
                <a href="{{ route('home') }}#peta" class="hover:text-blue transition">Peta GIS</a>
                <a href="{{ route('home') }}#data" class="hover:text-blue transition">Data</a>
                <a href="{{ route('home') }}#rekomendasi" class="hover:text-blue transition">Rekomendasi</a>
                <a href="{{ route('home') }}#tentang" class="hover:text-blue transition">Tentang</a>
            </div>
            <a href="{{ route('admin.login') }}" class="rounded-full btn-blue px-5 py-2.5 text-sm font-black">Masuk Admin</a>
        </div>
    </nav>

    @if (session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-24 sm:px-6 lg:px-8">
            <div class="animate-fadeUp rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="pt-20">
        @yield('content')
    </main>

    <footer class="border-t border-line bg-white">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 px-4 py-10 text-sm text-slate-500 sm:px-6 md:flex-row lg:px-8">
            <p>© {{ date('Y') }} <span class="font-black text-ink">GIS Perumahan</span>.</p>
            <p>Tailwind CDN · Leaflet CDN · OpenStreetMap</p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
