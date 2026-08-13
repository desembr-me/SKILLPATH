@extends('admin.layouts.app')
@section('title','Edit Jadwal Kelas | Admin SKILLPATH')
@section('page-title','Edit Jadwal Kelas')
@section('content')
<div class="admin-page-toolbar"><a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Kembali</a><span class="admin-status {{ $classSession->status }}">{{ strtoupper($classSession->status) }}</span></div>
<section class="admin-section-card">
    <x-admin.section-header eyebrow="Kelas Offline" title="{{ $classSession->title }}" description="Perbarui waktu dan detail tempat pelaksanaan." />
    <form method="POST" action="{{ route('admin.schedules.update',$classSession) }}" class="admin-form-stack">@csrf @method('PUT') @include('admin.schedules._form')<button class="admin-btn primary" type="submit">Simpan Perubahan</button></form>
</section>
@endsection
