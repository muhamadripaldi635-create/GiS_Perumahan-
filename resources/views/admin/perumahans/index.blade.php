@extends('layouts.admin')
@section('title', $recommendedOnly ? 'Kelola Rekomendasi Hunian' : 'Kelola Data Perumahan')
@section('page_title', $recommendedOnly ? 'Kelola Rekomendasi Hunian' : 'Kelola Data Perumahan')
@section('content')
@if($recommendedOnly)
    <div class="grid gap-5 mb-6 sm:grid-cols-3">
        <div class="card rounded-[2rem] p-6">
            <p class="text-sm font-bold text-slate-500">Total Hunian</p>
            <p class="mt-4 text-4xl font-black">{{ number_format($totalPerumahan, 0, ',', '.') }}</p>
        </div>
        <div class="card rounded-[2rem] p-6">
            <p class="text-sm font-bold text-slate-500">Rekomendasi Aktif</p>
            <p class="mt-4 text-4xl font-black">{{ number_format($recommendedTotal, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs font-semibold text-slate-500">Perumahan unggulan untuk pengguna</p>
        </div>
        <div class="card rounded-[2rem] p-6">
            <p class="text-sm font-bold text-slate-500">Permintaan Baru</p>
            <p class="mt-4 text-4xl font-black">0</p>
            <p class="mt-2 text-xs font-semibold text-slate-500">Tidak ada permintaan baru saat ini</p>
        </div>
    </div>
@endif
<div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
    <form class="flex w-full max-w-2xl flex-wrap items-center gap-3" method="GET">
        <input name="q" value="{{ request('q') }}" class="min-w-0 flex-1 rounded-2xl border-line px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="{{ $recommendedOnly ? 'Cari rekomendasi, perumahan, atau lokasi...' : 'Cari nama, alamat, kecamatan, kontak...' }}">
        @if($recommendedOnly)
            <input type="hidden" name="recommended" value="1">
        @endif
        <select name="kecamatan" class="rounded-2xl border border-line bg-white px-4 py-3 text-sm text-slate-600 focus:border-blue-600 focus:ring-blue-600">
            <option value="">Semua Wilayah</option>
            @foreach($kecamatanOptions as $kecamatan)
                <option value="{{ $kecamatan }}" @selected(request('kecamatan') === $kecamatan)>{{ $kecamatan }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-2xl border border-line bg-white px-4 py-3 text-sm text-slate-600 focus:border-blue-600 focus:ring-blue-600">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        <button class="rounded-2xl btn-blue px-5 py-3 text-sm font-black">Tampilkan</button>
    </form>
    <a href="{{ route('admin.perumahans.create') }}" class="rounded-full btn-blue px-5 py-3 text-center text-sm font-black">{{ $recommendedOnly ? 'Tambah Rekomendasi Baru' : 'Tambah Data' }}</a>
</div>
<div class="overflow-hidden rounded-[2rem] border border-line bg-white shadow-zenit">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-line text-sm">
            <thead class="bg-soft text-left text-xs font-black uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-4">Perumahan</th>
                    <th class="px-5 py-4">Lokasi</th>
                    <th class="px-5 py-4">Harga</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-center">Rekomendasi</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($perumahans as $item)
                    <tr class="hover:bg-soft/70 transition">
                        <td class="px-5 py-4 w-[35%]"><div class="flex items-center gap-3">
                            @if($item->images->first())
                                <img src="{{ asset('storage/'.$item->images->first()->path) }}" class="h-14 w-14 rounded-2xl object-cover">
                            @else
                                <div class="grid h-14 w-14 place-items-center rounded-2xl bg-soft text-xs font-black text-slate-400">IMG</div>
                            @endif
                            <div>
                                <p class="font-black">{{ $item->nama }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $item->jenis_perumahan ?: '-' }}</p>
                                @if($item->is_recommended)
                                    <span class="mt-2 inline-flex rounded-full bg-blue-100 px-2 py-1 text-[10px] font-black uppercase tracking-[.22em] text-blue-700">Rekomendasi</span>
                                @endif
                            </div>
                        </div></td>
                        <td class="px-5 py-4"><p class="font-bold">{{ $item->kecamatan ?: '-' }}</p><p class="mt-1 max-w-[280px] truncate text-xs text-slate-500">{{ $item->alamat ?: '-' }}</p></td>
                        <td class="px-5 py-4">
                            @if($item->harga_min || $item->harga_max)
                                <p class="font-black">{{ $item->harga_min ? 'Rp '.number_format($item->harga_min, 0, ',', '.') : '-' }}@if($item->harga_min && $item->harga_max) - {{ 'Rp '.number_format($item->harga_max, 0, ',', '.') }}@endif</p>
                            @else
                                <p class="font-black">-</p>
                            @endif
                        </td>
                        <td class="px-5 py-4"><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">{{ ucfirst(str_replace('_',' ', $item->status)) }}</span></td>
                        <td class="px-5 py-4 text-center">
                            <form method="POST" action="{{ route('admin.perumahans.toggleRecommended', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-9 w-16 items-center rounded-full border border-slate-200 bg-slate-100 transition focus:outline-none">
                                    <span class="absolute inset-y-1 left-1 h-7 w-7 rounded-full bg-white shadow transition duration-200 {{ $item->is_recommended ? 'translate-x-7 bg-blue-600' : 'translate-x-0 bg-slate-300' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-4"><div class="flex justify-end gap-2">
                            <a href="{{ route('perumahan.show', $item) }}" target="_blank" class="rounded-full border border-line px-3 py-2 text-xs font-black hover:bg-soft">Publik</a>
                            <a href="{{ route('admin.perumahans.edit', $item) }}" class="rounded-full bg-ink px-3 py-2 text-xs font-black text-white">Edit</a>
                            <form method="POST" action="{{ route('admin.perumahans.destroy', $item) }}" onsubmit="return confirm('Hapus data perumahan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-full bg-rose-600 px-3 py-2 text-xs font-black text-white">Hapus</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center font-bold text-slate-500">Belum ada data perumahan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-line px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">Menampilkan {{ $perumahans->firstItem() ?? 0 }}–{{ $perumahans->lastItem() ?? 0 }} dari {{ $perumahans->total() }} rekomendasi</p>
        {{ $perumahans->links() }}
    </div>
</div>
@endsection
