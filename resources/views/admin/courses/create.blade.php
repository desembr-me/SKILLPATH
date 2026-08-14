@extends('admin.layouts.app')
@section('title', 'Tambah Course | Admin SKILLPATH')
@section('page-title', 'Tambah Course')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.courses.store') }}">
    @csrf
    @include('admin.courses._form')
</form>
@endsection
