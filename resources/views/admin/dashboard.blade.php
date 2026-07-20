@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('content')
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([['Total Perumahan',$totalPerumahan,'holiday_village'],['Kecamatan',$totalKecamatan,'map'],['Total Risiko',$totalRisiko,'warning'],['Risiko Tinggi/Kritis',$risikoTinggi,'priority_high']] as $card)
        <div class="card rounded-[2rem] p-6"><span class="material-symbols-outlined text-blue-600">{{ $card[2] }}</span><p class="mt-4 text-sm font-bold text-slate-500">{{ $card[0] }}</p><p class="mt-2 text-4xl font-black">{{ $card[1] }}</p></div>
    @endforeach
</div>
<div class="mt-7 grid gap-7 xl:grid-cols-2">
    <section class="card rounded-[2rem] p-6"><div class="flex items-center justify-between"><h2 class="text-xl font-black">Data Perumahan Terbaru</h2><a href="{{ route('admin.perumahans.index') }}" class="text-sm font-black text-blue-600">Lihat semua</a></div><div class="mt-5 space-y-3">@forelse($perumahanTerbaru as $item)<div class="rounded-2xl border border-line bg-soft p-4"><p class="font-black">{{ $item->nama }}</p><p class="mt-1 text-sm text-slate-500">{{ $item->kecamatan ?: '-' }} · {{ $item->risikos_count }} risiko</p></div>@empty<p class="text-sm font-bold text-slate-500">Belum ada data.</p>@endforelse</div></section>
    <section class="card rounded-[2rem] p-6"><div class="flex items-center justify-between"><h2 class="text-xl font-black">Risiko Terbaru</h2><a href="{{ route('admin.risikos.index') }}" class="text-sm font-black text-blue-600">Kelola risiko</a></div><div class="mt-5 space-y-3">@forelse($risikoTerbaru as $item)<div class="rounded-2xl border border-line bg-soft p-4"><p class="font-black">{{ $item->nama_risiko }}</p><p class="mt-1 text-sm text-slate-500">{{ optional($item->perumahan)->nama ?: '-' }} · {{ ucfirst($item->tingkat) }}</p></div>@empty<p class="text-sm font-bold text-slate-500">Belum ada risiko.</p>@endforelse</div></section>
</div>
@endsection
