@extends('layouts.admin')
@section('title', 'Tambah Risiko')
@section('page_title', 'Tambah Risiko')
@section('content')
@include('admin.risikos._form', ['action' => route('admin.risikos.store'), 'method' => 'POST', 'submit' => 'Simpan Risiko'])
@endsection
