@extends('layouts.app')
@section('title','Jalur Belajar '.$child->name)
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Adaptive Learning Path</span>
            <h1>Jalur Belajar {{ $child->name }}</h1>
            <p>{{ $path->rationale }}</p>
        </div>
    </div>
    <div class="learning-path-intro"><span class="path-intro-icon"><x-icon name="path" /></span><div><b>Rekomendasi dapat berkembang</b><p>Urutan course dapat berubah mengikuti minat, usia, course yang sudah diikuti, serta perkembangan anak.</p></div></div>
    <div class="path-list">
        @foreach($path->items as $item)
        <article class="path-item-card">
            <div class="path-item">
                <div class="path-number">{{ str_pad($item->sequence,2,'0',STR_PAD_LEFT) }}</div>
                <div class="path-visual" style="--path-accent:{{ $item->course->accent }}"><x-icon :name="$item->course->category->slug" /></div>
                <div><span class="status-chip {{ $item->status }}">{{ ucfirst($item->status) }}</span><h3>{{ $item->course->title }}</h3><p>{{ $item->reason }}</p><small>Match {{ $item->match_score }}% • {{ $item->course->category->name }}</small></div>
                <a class="btn btn-soft" href="{{ route('courses.show',$item->course) }}">Lihat Course</a>
            </div>
            <details class="child-voice-block" @if($item->child_voice) open @endif>
                <summary><x-icon name="review" /> Catatan Minat Anak @if($item->child_voice)<span class="status-chip recommended">Tersimpan</span>@endif</summary>
                <div class="child-voice-body">
                    <p class="child-voice-hint">Catat apa kata {{ $child->nickname ?: $child->name }} tentang rekomendasi ini, dengan kata-katanya sendiri.</p>
                    <form method="POST" action="{{ route('parent.learning-path.voice',$item) }}">
                        @csrf
                        @method('PUT')
                        <div style="margin-bottom:10px;">
                            <textarea id="voice_text_{{ $item->id }}" name="child_voice" rows="2" placeholder="Contoh: Aku suka karena ada bikin robotnya..." style="width:100%; border:1.5px solid #e2e8f0; border-radius:12px; padding:10px 14px; font-size:12px;">{{ $item->child_voice }}</textarea>
                        </div>
                        <button class="btn btn-primary btn-sm"><x-icon name="check" /> Simpan Catatan</button>
                    </form>
                </div>
            </details>
        </article>
        @endforeach
    </div>
</section>
@endsection
