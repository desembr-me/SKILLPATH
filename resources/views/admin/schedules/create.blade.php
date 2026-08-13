@extends('admin.layouts.app')
@section('title','Tambah Jadwal Kelas | Admin SKILLPATH')
@section('page-title','Tambah Jadwal Kelas')
@section('content')
<div class="admin-page-toolbar"><a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Kembali</a></div>
<section class="admin-section-card">
    <x-admin.section-header eyebrow="Kelas Offline" title="Buat jadwal tatap muka" description="Tentukan waktu, kapasitas, lokasi, alamat, dan persiapan peserta." />
    <form method="POST" action="{{ route('admin.schedules.store') }}" class="admin-form-stack">@csrf @include('admin.schedules._form')<button class="admin-btn primary" type="submit">Simpan Jadwal</button></form>
</section>
@endsection
