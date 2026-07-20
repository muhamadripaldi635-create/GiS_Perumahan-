@extends('layouts.admin')
@section('title', 'Edit Data Perumahan')
@section('page_title', 'Edit Data Perumahan')
@section('content')
@include('admin.perumahans._form', ['action' => route('admin.perumahans.update', $perumahan), 'method' => 'PUT', 'submit' => 'Perbarui Data Perumahan'])
@endsection
