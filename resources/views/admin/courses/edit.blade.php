@extends('admin.layouts.app')
@section('title', 'Edit Course | Admin SKILLPATH')
@section('page-title', 'Edit Course')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.courses.update', $course) }}">
    @csrf
    @method('PUT')
    @include('admin.courses._form')
</form>
@endsection
