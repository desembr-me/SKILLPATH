@extends('layouts.app')

@section('title', 'Sertifikat | '.$learningPath->title)

@section('content')
<section class="skill-certificate-page">
    <div class="container">
        <div class="skill-certificate-toolbar">
            <div>
                <span class="skill-cert-page-kicker">Sertifikat Kelas</span>
                <h1>{{ $learningPath->title }}</h1>
                <p>Sertifikat tersedia setelah persyaratan kehadiran kelas terpenuhi.</p>
            </div>

            <div class="skill-certificate-actions">
                <a class="btn btn-light" href="{{ route('my-courses.index') }}">← Kelas Saya</a>
                <button class="btn btn-dark" type="button" onclick="window.print()">
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <x-certificate.document
            :certificate="$certificate"
            :child="$child"
            :course="$learningPath"
        />

        <div class="skill-certificate-note">
            <strong>Catatan</strong>
            <span>
                Gunakan menu <b>Cetak / Simpan PDF</b> untuk menyimpan sertifikat dalam ukuran A4 landscape.
                Sertifikat yang dicabut administrator tidak dapat digunakan.
            </span>
        </div>
    </div>
</section>
@endsection
