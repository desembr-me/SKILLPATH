@extends('layouts.app')
@section('title','Co-Design Minat Anak')
@section('content')
<x-parent-nav />
<section class="onboarding-wrap">
    <div class="onboarding-card">
        <div class="onboarding-header">
            <div><span class="eyebrow">Co-Design Onboarding</span><h1>Orang tua menemani. Anak memilih.</h1><p class="lead">Ajak anak menentukan minatnya langsung. Pilih maksimal tiga kategori yang benar-benar ingin mereka coba.</p></div>
            <div class="onboarding-visual"><span class="dot d1"></span><span class="dot d2"></span><x-icon name="co-design" /></div>
        </div>
        <form method="POST" action="{{ route('parent.onboarding.store') }}" id="interestForm">@csrf
            <div class="form-grid"><label>Nama anak<input name="name" required placeholder="Contoh: Alya"></label><label>Tanggal lahir<input type="date" name="birth_date" required></label></div>
            <div class="form-section-title"><div><span>01</span><h3>Apa yang paling ingin kamu lakukan?</h3></div><small>Pilih maksimal 3 minat</small></div>
            <div class="interest-select">
                @foreach([['Arts','Membuat karya'],['Music','Main musik'],['Languages','Belajar bahasa'],['Sports','Bergerak aktif'],['Self Improvement','Makin percaya diri'],['Technology','Eksperimen teknologi']] as $item)
                <label>
                    <input type="checkbox" name="interests[]" value="{{ $item[0] }}">
                    <span class="interest-icon"><x-icon :name="$item[0]" /></span>
                    <b>{{ $item[0] }}</b><small>{{ $item[1] }}</small><i class="select-indicator"></i>
                </label>
                @endforeach
            </div>
            <div class="form-section-title"><div><span>02</span><h3>Gaya belajar yang terasa nyaman</h3></div><small>Boleh pilih lebih dari satu</small></div>
            <div class="preference-list">
                @foreach(['hands_on'=>['Mencoba langsung','Belajar lewat praktik dan eksperimen'],'group'=>['Bersama teman','Nyaman belajar dalam kelompok kecil'],'step_by_step'=>['Tantangan bertahap','Suka instruksi yang jelas dan berurutan'],'storytelling'=>['Bercerita atau tampil','Suka menyampaikan ide dan presentasi']] as $k=>$v)
                <label><input type="checkbox" name="learning_preferences[]" value="{{ $k }}"><span><b>{{ $v[0] }}</b><small>{{ $v[1] }}</small></span></label>
                @endforeach
            </div>
            <div class="form-section-title"><div><span>03</span><h3>Suara Anak</h3></div><small>Cooperative Inquiry</small></div>
            <div class="form-grid"><label>Apa kata anak tentang pilihannya? (opsional)<textarea name="child_voice" rows="3" placeholder="Tuliskan dengan kata-kata anak sendiri, misalnya alasan mereka memilih minat ini...">{{ old('child_voice') }}</textarea></label></div>
            <label class="confirm-check"><input type="checkbox" name="discussed_with_child" value="1" required @checked(old('discussed_with_child'))> <span>Saya (orang tua) sudah mendiskusikan pilihan ini bersama anak, bukan memutuskan sendiri.</span></label>
            <div class="onboarding-submit"><p>Jawaban anak digunakan untuk membuat rekomendasi awal. Orang tua tetap dapat memilih course yang paling sesuai.</p><button class="btn btn-primary btn-lg">Buat Jalur Belajar <x-icon name="arrow-right" /></button></div>
        </form>
    </div>
</section>
@endsection
