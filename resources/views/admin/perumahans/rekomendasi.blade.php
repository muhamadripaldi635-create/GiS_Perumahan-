@extends('layouts.admin')
@section('title', 'Kelola Rekomendasi Perumahan')
@section('page_title', 'Rekomendasi Perumahan')
@section('content')
    <section class="mb-6 rounded-[2rem] bg-slate-50 p-6 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-4xl">
                <p class="text-xs font-black uppercase tracking-[.3em] text-slate-400">GIS Perumahan</p>
                <h1 class="mt-3 text-4xl font-black text-slate-950"> Rekomendasi Lokasi Perumahan</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Atur rekomendasi perumahan unggulan, pantau status rekomendasi, dan tinjau permintaan baru dalam satu tampilan yang bersih dan mudah digunakan.</p>
            </div>
            <div class="flex items-center justify-start">
            </div>
        </div>
    </section>

    <section class="grid gap-5 mb-6 sm:grid-cols-3">
        <div class="rounded-[2rem] bg-white p-6 shadow-zenit">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-slate-500">Total Perumahan</p>
                    <p class="mt-4 text-4xl font-black text-slate-950">{{ number_format($totalPerumahan, 0, ',', '.') }}</p>
                </div>
                <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-700">{{ $growthPercentage }}%</span>
            </div>
            <p class="mt-4 text-xs text-slate-500">Pertumbuhan dalam 30 hari terakhir</p>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-zenit">
            <p class="text-sm font-bold text-slate-500">Rekomendasi Aktif</p>
            <p class="mt-4 text-4xl font-black text-slate-950">{{ number_format($recommendedTotal, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs font-semibold text-slate-500">Status Optimal</p>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-zenit">
            <p class="text-sm font-bold text-slate-500">Permintaan Baru</p>
            <p class="mt-4 text-4xl font-black text-slate-950">{{ $pendingRequests }}</p>
            <p class="mt-2 text-xs font-semibold text-slate-500">{{ $pendingStatus }}</p>
        </div>
    </section>

    <section class="mb-6 rounded-[2rem] bg-white p-6 shadow-zenit">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form class="flex w-full flex-wrap items-center gap-3" method="GET">
                <input name="q" value="{{ request('q') }}" class="min-w-[220px] flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Cari nama perumahan...">
                <select name="kecamatan" class="min-w-[180px] rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Wilayah</option>
                    @foreach($kecamatanOptions as $kecamatan)
                        <option value="{{ $kecamatan }}" @selected(request('kecamatan') === $kecamatan)>{{ $kecamatan }}</option>
                    @endforeach
                </select>
                <select name="status" class="min-w-[160px] rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700">Filter</button>
            </form>
            <div class="flex flex-wrap items-center gap-3 justify-end">
                <a href="{{ route('admin.perumahans.rekomendasi.export', request()->query()) }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-100">Export</a>
                <button type="button" class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-black text-slate-700 hover:bg-slate-200">Filter Lanjutan</button>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-[2rem] bg-white shadow-zenit">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Nama Perumahan</th>
                        <th class="px-5 py-4">Lokasi</th>
                        <th class="px-5 py-4">Harga</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($perumahans as $item)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-5 py-4 w-[40%]"><div class="flex items-center gap-4">
                                @if($item->images->first())
                                    <img src="{{ asset('storage/'.$item->images->first()->path) }}" class="h-16 w-16 rounded-2xl object-cover">
                                @else
                                    <div class="grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-xs font-black text-slate-400">IMG</div>
                                @endif
                                <div>
                                    <p class="font-black text-slate-950">{{ $item->nama }}</p>
                                    <p class="mt-1 text-xs text-slate-500">ID: {{ $item->id }}</p>
                                </div>
                            </div></td>
                            <td class="px-5 py-4"><p class="font-bold text-slate-950">{{ $item->kecamatan ?: '-' }}</p><p class="mt-1 text-xs text-slate-500">{{ $item->kabupaten ?: '-' }}</p></td>
                            <td class="px-5 py-4"><p class="font-black text-slate-950">@if($item->harga_min || $item->harga_max){{ $item->harga_min ? 'Rp '.number_format($item->harga_min, 0, ',', '.') : '-' }}@if($item->harga_min && $item->harga_max) - {{ 'Rp '.number_format($item->harga_max, 0, ',', '.') }}@endif @else - @endif</p></td>
                            <td class="px-5 py-4"><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td>
                            <td class="px-5 py-4"><div class="flex justify-end flex-wrap gap-2">
                                <a href="{{ route('perumahan.show', $item) }}" target="_blank" class="rounded-full border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-100">Detail</a>
                                <a href="{{ route('admin.perumahans.edit', $item) }}" class="rounded-full bg-ink px-3 py-2 text-xs font-black text-white">Edit</a>
                                <form method="POST" action="{{ route('admin.perumahans.destroy', $item) }}" onsubmit="return confirm('Hapus data perumahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full bg-rose-600 px-3 py-2 text-xs font-black text-white">Hapus</button>
                                </form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center font-bold text-slate-500">Belum ada rekomendasi perumahan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Menampilkan {{ $perumahans->firstItem() ?? 0 }}–{{ $perumahans->lastItem() ?? 0 }} dari {{ $perumahans->total() }} rekomendasi</p>
            {{ $perumahans->links() }}
        </div>
    </section>
@endsection
