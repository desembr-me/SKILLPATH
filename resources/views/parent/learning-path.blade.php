@extends('layouts.app')
@section('title','Jalur Belajar '.$child->name)
@section('content')
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Adaptive Learning Path</span><h1>Jalur Belajar {{ $child->name }}</h1><p>{{ $path->rationale }}</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
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
                <summary><x-icon name="mic" /> Suara Anak @if($item->child_voice)<span class="status-chip recommended">Tersimpan</span>@endif</summary>
                <div class="child-voice-body">
                    <p class="child-voice-hint">Catat apa kata {{ $child->nickname ?: $child->name }} tentang rekomendasi ini, dengan kata-katanya sendiri.</p>
                    <form method="POST" action="{{ route('parent.learning-path.voice',$item) }}">
                        @csrf
                        @method('PUT')
                        <div class="voice-input-container" style="margin-bottom:10px;">
                            <div class="voice-input-header">
                                <span class="voice-label" style="font-size:11px; color:var(--muted);">Ungkapan anak:</span>
                                <div class="voice-actions">
                                    <button type="button" class="btn-voice-speak" style="{{ $item->child_voice ? '' : 'display:none;' }}" onclick="speakPathVoice(this, 'voice_text_{{ $item->id }}')" title="Dengarkan kata-kata anak">
                                        <x-icon name="volume" /> <span>Dengarkan</span>
                                    </button>
                                    <button type="button" class="btn-voice-record" onclick="togglePathVoice(this, 'voice_text_{{ $item->id }}')" title="Rekam suara anak">
                                        <x-icon name="mic" /> <span>Bicara / Rekam</span>
                                    </button>
                                </div>
                            </div>
                            <div class="voice-textarea-box">
                                <textarea id="voice_text_{{ $item->id }}" name="child_voice" rows="2" placeholder="Contoh: Aku suka karena ada bikin robotnya...">{{ $item->child_voice }}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-soft btn-sm"><x-icon name="mic" /> Simpan Suara Anak</button>
                    </form>
                </div>
            </details>
        </article>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
let activeRecognition = null;
let currentBtn = null;

function togglePathVoice(btn, textareaId) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert('Fitur pengenalan suara tidak didukung oleh peramban ini. Anda tetap dapat mengetik secara manual.');
        return;
    }

    const textarea = document.getElementById(textareaId);
    if (!textarea) return;

    if (activeRecognition) {
        activeRecognition.stop();
        activeRecognition = null;
        if (currentBtn) {
            currentBtn.classList.remove('recording');
            currentBtn.querySelector('span').textContent = 'Bicara / Rekam';
        }
        if (currentBtn === btn) return;
    }

    const recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'id-ID';

    let initial = textarea.value ? textarea.value.trim() + ' ' : '';

    recognition.onstart = function() {
        activeRecognition = recognition;
        currentBtn = btn;
        btn.classList.add('recording');
        btn.querySelector('span').textContent = 'Berhenti';
    };

    recognition.onresult = function(event) {
        let interim = '';
        let finalStr = '';
        for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) finalStr += event.results[i][0].transcript;
            else interim += event.results[i][0].transcript;
        }
        const current = (finalStr || interim).trim();
        if (current) textarea.value = initial + current;
    };

    recognition.onerror = function() {
        if (btn) {
            btn.classList.remove('recording');
            btn.querySelector('span').textContent = 'Bicara / Rekam';
        }
        activeRecognition = null;
    };

    recognition.onend = function() {
        if (btn) {
            btn.classList.remove('recording');
            btn.querySelector('span').textContent = 'Bicara / Rekam';
        }
        activeRecognition = null;
    };

    try {
        recognition.start();
    } catch (e) {
        console.error(e);
    }
}

function speakPathVoice(btn, textareaId) {
    if (!('speechSynthesis' in window)) return;
    const textarea = document.getElementById(textareaId);
    if (!textarea || !textarea.value.trim()) return;

    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(textarea.value.trim());
    utterance.lang = 'id-ID';
    utterance.rate = 0.95;

    btn.classList.add('speaking');
    btn.querySelector('span').textContent = 'Memutar...';

    utterance.onend = function() {
        btn.classList.remove('speaking');
        btn.querySelector('span').textContent = 'Dengarkan';
    };
    utterance.onerror = function() {
        btn.classList.remove('speaking');
        btn.querySelector('span').textContent = 'Dengarkan';
    };

    window.speechSynthesis.speak(utterance);
}
</script>
@endpush
@endsection
