<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin GIS Perumahan')</title>
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
                    keyframes: { fadeUp: { '0%': { opacity: 0, transform: 'translateY(12px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } } },
                    animation: { fadeUp: 'fadeUp .45s ease both' }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 600,'GRAD' 0,'opsz' 24; vertical-align:middle; }
        input, textarea, select { outline: none; }
        .admin-map { height: 420px; min-height: 320px; background:#f6f7f9; }
        .leaflet-container { font-family: Inter, sans-serif; }
        .leaflet-popup-content-wrapper { border-radius:18px; box-shadow:0 24px 70px rgba(15,23,42,.18); border:1px solid #e7e9ee; }
        .pick-marker { width:26px; height:26px; border-radius:999px; background:#2563eb; border:4px solid #fff; box-shadow:0 15px 40px rgba(37,99,235,.35),0 0 0 8px rgba(37,99,235,.14); }
        .existing-marker { width:14px; height:14px; border-radius:999px; background:#050505; border:3px solid white; box-shadow:0 6px 18px rgba(15,23,42,.25); }
        .btn-blue { background:#2563eb; color:#fff; transition:all .2s ease; }
        .btn-blue:hover { background:#1d4ed8; transform:translateY(-1px); box-shadow:0 16px 34px rgba(37,99,235,.22); }
        .btn-black { background:#050505; color:#fff; transition:all .2s ease; }
        .btn-black:hover { background:#111827; transform:translateY(-1px); }
        .card { border:1px solid #e7e9ee; background:#fff; box-shadow:0 16px 60px rgba(15,23,42,.05); }
        @media print { .no-print { display:none!important; } main { padding:0!important; } }
    </style>
    @stack('head')
</head>
<body class="bg-soft font-sans text-ink antialiased">
<div class="min-h-screen lg:flex">
    <aside id="admin-sidebar" class="no-print border-r border-line bg-[#00008b] text-white lg:sticky lg:top-0 lg:h-screen lg:w-72 lg:shrink-0">
        <div class="border-b border-white/20 px-5 py-4 lg:px-6 lg:py-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-white text-sm font-black text-[#1e40af]">Z</span>
                <span>
                    <span class="block text-sm font-black">Admin</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[.22em] text-white/70">Data perumahan</span>
                </span>
            </a>
        </div>
        <nav class="grid gap-1 px-4 py-4 text-sm font-bold lg:py-6">
            @php
                $items = [
                    ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                    ['route' => 'admin.perumahans.index', 'active' => 'admin.perumahans.index', 'icon' => 'holiday_village', 'label' => 'Kelola Perumahan'],
                    ['route' => 'admin.perumahans.rekomendasi', 'active' => 'admin.perumahans.rekomendasi', 'icon' => 'thumb_up', 'label' => 'Rekomendasi Perumahan'],
                    ['route' => 'admin.risikos.index', 'active' => 'admin.risikos.*', 'icon' => 'warning', 'label' => 'Kelola Risiko'],
                    ['route' => 'admin.laporan.index', 'active' => 'admin.laporan.*', 'icon' => 'folder_open', 'label' => 'Laporan'],
                ];
            @endphp
            @foreach ($items as $item)
                @php
                    $isActive = request()->routeIs($item['active'])
                        && (!isset($item['recommended'])
                            ? true
                            : ($item['recommended'] ? request('recommended') === '1' : request('recommended') !== '1'));
                @endphp
                <a href="{{ route($item['route'], $item['query'] ?? []) }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 transition {{ $isActive ? 'bg-white/15 text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>{{ $item['label'] }}
                </a>
            @endforeach
            <div class="my-3 border-t border-white/20"></div>
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-white/90 transition hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px]">open_in_new</span>Lihat Publik
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-slate-600 transition hover:bg-rose-50 hover:text-rose-600">
                    <span class="material-symbols-outlined text-[20px]">logout</span>Keluar
                </button>
            </form>
        </nav>
    </aside>
    <div class="min-w-0 flex-1">
        <header class="no-print sticky top-0 z-40 border-b border-line bg-white/86 backdrop-blur-xl">
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[.25em] text-slate-400">GIS Perumahan</p>
                    <h1 class="text-xl font-black tracking-tight">@yield('page_title', 'Dashboard')</h1>
                </div>
                <!-- <a href="{{ route('admin.perumahans.create') }}" class="hidden rounded-full btn-blue px-5 py-2.5 text-sm font-black sm:inline-flex">Tambah Data</a> -->
            </div>
        </header>
        <main class="p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-6 animate-fadeUp rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <p class="font-black">Ada data yang belum valid:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@stack('scripts')
</body>
</html>
