@extends('layouts.admin')
@section('title', 'Tambah Perumaha Baeru')
@section('page_title', 'Tambah Perumaha Baeru')
@section('content')
<div class="mx-auto max-w-7xl space-y-8">
    <section class="rounded-[2rem] bg-white p-8 shadow-zenit border border-slate-200">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-black uppercase tracking-[.3em] text-slate-400">GIS Perumahan</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950">Tambah Perumaha Baru</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">Isi detail perumahan unggulan yang ingin ditampilkan sebagai rekomendasi. Gunakan data lengkap agar rekomendasi terlihat profesional dan mudah ditemukan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.perumahans.rekomendasi') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">Kembali ke Rekomendasi</a>
                <button type="submit" form="recommendation-form" class="min-w-[170px] rounded-full px-6 py-3 text-sm font-black text-white shadow-lg transition hover:bg-blue-900" style="background-color:#072b57;">Simpan Data</button>
            </div>
        </div>
    </section>

    @include('admin.perumahans._form', [
        'action' => route('admin.perumahans.store'),
        'method' => 'POST',
        'submit' => 'Simpan Rekomendasi',
        'recommendationMode' => true,
    ])
</div>
@endsection

