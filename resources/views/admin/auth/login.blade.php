<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - GIS Perumahan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white font-sans text-[#050505]">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="hidden border-r border-slate-200 bg-[#00008b] p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-sm font-black text-[#050505]">Z</span><div><p class="font-black">Admin</p><p class="text-xs text-slate-400">GIS Perumahan</p></div></div>
            <div class="max-w-lg"><p class="mb-4 text-xs font-black uppercase tracking-[.3em] text-blue-300">Control Center</p><h1 class="text-5xl font-black leading-tight">Kelola data perumahan, risiko, laporan, dan titik GIS.</h1><p class="mt-5 text-slate-300">Akses admin bersifat terbatas. Halaman publik tetap dapat dilihat tanpa login.</p></div>
            <p class="text-xs text-slate-500"></p>
        </section>
        <section class="flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,.08)]">
                <p class="text-xs font-black uppercase tracking-[.3em] text-blue-600">Login Admin</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight">Masuk Sistem</h2>
                @if ($errors->any())<div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-7 space-y-5">
                    @csrf
                    <label class="block"><span class="text-sm font-black">Email</span><input name="email" type="email" required value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                    <label class="block"><span class="text-sm font-black">Password</span><input name="password" type="password" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                    <button class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-blue-700">Masuk Admin</button>
                </form>
                <a href="{{ route('home') }}" class="mt-5 block text-center text-sm font-bold text-slate-500 hover:text-blue-600">Kembali ke halaman publik</a>
            </div>
        </section>
    </div>
</body>
</html>
