@extends('layouts.admin')
@section('title', 'Edit Risiko')
@section('page_title', 'Edit Risiko')
@section('content')
@include('admin.risikos._form', ['action' => route('admin.risikos.update', $risiko), 'method' => 'PUT', 'submit' => 'Perbarui Risiko'])
@endsection
