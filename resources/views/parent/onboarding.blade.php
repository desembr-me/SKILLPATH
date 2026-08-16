@extends('layouts.app')
@section('title','Co-Design Minat Anak')
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Co-Design Onboarding</span>
            <h1>Orang Tua Menemani. Anak Memilih.</h1>
            <p>Ajak anak menentukan minatnya langsung. Pilih maksimal tiga kategori yang benar-benar ingin mereka coba.</p>
        </div>
    </div>

    <div class="panel onboarding-panel">
        <form method="POST" action="{{ route('parent.onboarding.store') }}" id="interestForm">
            @csrf
            <div class="form-grid">
                <label>
                    Nama Anak
                    <input name="name" required placeholder="Contoh: Alya" value="{{ old('name') }}" class="form-control">
                </label>
                <label>
                    Tanggal Lahir
                    <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="form-control">
                </label>
            </div>

            <div class="form-section-title">
                <div><span>01</span><h3>Apa yang paling ingin kamu lakukan?</h3></div>
                <small>Pilih maksimal 3 minat</small>
            </div>
            <div class="interest-select">
                @foreach([
                    ['Arts','Membuat karya'],
                    ['Music','Main musik'],
                    ['Languages','Belajar bahasa'],
                    ['Sports','Bergerak aktif'],
                    ['Self Improvement','Makin percaya diri'],
                    ['Technology','Eksperimen teknologi']
                ] as $item)
                <label>
                    <input type="checkbox" name="interests[]" value="{{ $item[0] }}">
                    <span class="interest-icon"><x-icon :name="$item[0]" /></span>
                    <b>{{ $item[0] }}</b>
                    <small>{{ $item[1] }}</small>
                    <i class="select-indicator"></i>
                </label>
                @endforeach
            </div>

            <div class="form-section-title">
                <div><span>02</span><h3>Gaya belajar yang terasa nyaman</h3></div>
                <small>Boleh pilih lebih dari satu</small>
            </div>
            <div class="preference-list">
                @foreach([
                    'hands_on'=>['Mencoba langsung','Belajar lewat praktik dan eksperimen'],
                    'group'=>['Bersama teman','Nyaman belajar dalam kelompok kecil'],
                    'step_by_step'=>['Tantangan bertahap','Suka instruksi yang jelas dan berurutan'],
                    'storytelling'=>['Bercerita atau tampil','Suka menyampaikan ide dan presentasi']
                ] as $k=>$v)
                <label>
                    <input type="checkbox" name="learning_preferences[]" value="{{ $k }}">
                    <span><b>{{ $v[0] }}</b><small>{{ $v[1] }}</small></span>
                </label>
                @endforeach
            </div>

            <div class="form-section-title">
                <div><span>03</span><h3>Suara Anak</h3></div>
                <small>Pendapat & Keinginan Anak</small>
            </div>
            
            {{-- Modern Interactive Voice Note Card --}}
            <div class="voice-input-card">
                <div class="voice-card-header">
                    <div>
                        <label for="childVoiceTextarea" class="voice-card-label">
                            Apa kata anak tentang pilihannya? <span class="opt-badge">(opsional)</span>
                        </label>
                        <p class="voice-card-desc">Gunakan tombol rekam untuk mendengarkan & mencatat langsung kata-kata anak.</p>
                    </div>
                    <div class="voice-card-actions">
                        <button type="button" class="btn-voice-pill btn-voice-mic" id="btnRecordVoice" title="Rekam suara anak (Dikte otomatis)">
                            <span class="mic-dot"></span>
                            <x-icon name="mic" />
                            <span id="btnRecordVoiceLabel">Rekam Suara Anak</span>
                        </button>
                        <button type="button" class="btn-voice-pill btn-voice-tts" id="btnPlayVoice" title="Dengarkan kembali suara yang tercatat" style="display:none;">
                            <x-icon name="volume" />
                            <span id="btnPlayVoiceLabel">Dengarkan</span>
                        </button>
                        <button type="button" class="btn-voice-pill btn-voice-reset" id="btnClearVoice" title="Hapus catatan suara" style="display:none;">
                            <x-icon name="trash" />
                        </button>
                    </div>
                </div>

                {{-- Live Recording Visualizer Banner --}}
                <div class="voice-live-banner" id="voiceLiveBanner" style="display:none;">
                    <div class="voice-live-indicator">
                        <span class="live-rec-dot"></span>
                        <span class="live-rec-status" id="voiceLiveStatus">Mendengarkan anak berbicara...</span>
                        <span class="live-rec-timer" id="voiceTimer">00:00</span>
                    </div>
                    <div class="voice-live-waves">
                        <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <button type="button" class="btn-voice-stop" id="btnStopVoice">
                        <x-icon name="stop" /> Selesai Rekam
                    </button>
                </div>

                {{-- Textarea Box with Visual State --}}
                <div class="voice-textarea-wrapper" id="voiceTextWrapper">
                    <textarea 
                        name="child_voice" 
                        id="childVoiceTextarea" 
                        rows="3" 
                        class="voice-textarea-control"
                        placeholder="Contoh: Alya ingin belajar menggambar karena suka membuat karakter kartun sendiri..."
                    >{{ old('child_voice') }}</textarea>
                    <div class="voice-char-counter">
                        <span id="voiceCharCount">0</span>/1000
                    </div>
                </div>

                {{-- Quick Voice Idea Starter Chips --}}
                <div class="voice-quick-chips">
                    <span class="quick-chip-label">✨ Inspirasi Cepat Suara Anak:</span>
                    <div class="quick-chips-list">
                        <button type="button" class="quick-voice-chip" data-text="Aku suka menggambar dan mau bisa buat komik serta karakter kartun sendiri!">🎨 Buat kartun & komik</button>
                        <button type="button" class="quick-voice-chip" data-text="Aku pengen belajar merakit robot pintar yang bisa jalan sendiri!">🤖 Mau merakit robot</button>
                        <button type="button" class="quick-voice-chip" data-text="Aku mau bisa bikin game seru buat dimainkan bareng teman!">🎮 Ingin buat game</button>
                        <button type="button" class="quick-voice-chip" data-text="Aku suka dengerin musik dan pengen lancar main alat musik!">🎵 Belajar musik</button>
                        <button type="button" class="quick-voice-chip" data-text="Aku mau percaya diri ngomong bahasa Inggris ke orang lain!">🗣️ Percakapan bahasa</button>
                    </div>
                </div>
            </div>

            <label class="confirm-check">
                <input type="checkbox" name="discussed_with_child" value="1" required @checked(old('discussed_with_child'))> 
                <span>Saya (orang tua) sudah mendiskusikan pilihan ini bersama anak, bukan memutuskan sendiri.</span>
            </label>

            <div class="onboarding-submit">
                <p>Jawaban anak digunakan untuk membuat rekomendasi awal. Orang tua tetap dapat memilih course yang paling sesuai.</p>
                <button class="btn btn-primary btn-lg">Buat Jalur Belajar <x-icon name="arrow-right" /></button>
            </div>
        </form>
    </div>
</section>

{{-- Voice Note Audio & Speech Recognition Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('childVoiceTextarea');
    const textWrapper = document.getElementById('voiceTextWrapper');
    const btnRecord = document.getElementById('btnRecordVoice');
    const btnRecordLabel = document.getElementById('btnRecordVoiceLabel');
    const btnPlay = document.getElementById('btnPlayVoice');
    const btnPlayLabel = document.getElementById('btnPlayVoiceLabel');
    const btnClear = document.getElementById('btnClearVoice');
    const banner = document.getElementById('voiceLiveBanner');
    const timerEl = document.getElementById('voiceTimer');
    const btnStop = document.getElementById('btnStopVoice');
    const charCountEl = document.getElementById('voiceCharCount');
    const quickChips = document.querySelectorAll('.quick-voice-chip');

    let isRecording = false;
    let isSpeaking = false;
    let recognition = null;
    let timerInterval = null;
    let secondsElapsed = 0;
    let currentSpeechSynthesis = window.speechSynthesis || null;
    let activeUtterance = null;

    // 1. Update Character Counter & Button Visibility
    function updateState() {
        const val = textarea.value || '';
        if (charCountEl) charCountEl.textContent = val.length;
        
        if (val.trim().length > 0) {
            if (btnPlay) btnPlay.style.display = 'inline-flex';
            if (btnClear) btnClear.style.display = 'inline-flex';
        } else {
            if (btnPlay && !isSpeaking) btnPlay.style.display = 'none';
            if (btnClear) btnClear.style.display = 'none';
        }
    }

    textarea.addEventListener('input', updateState);
    updateState();

    // 2. Speech Recognition Setup (Web Speech API)
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    function startSpeechRecognition() {
        if (!SpeechRecognition) {
            alert('Browser ini belum mendukung Web Speech Recognition langsung. Anda dapat mengetik atau menggunakan browser Google Chrome/Edge.');
            return false;
        }

        try {
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.continuous = true;
            recognition.interimResults = true;

            let finalTranscriptBefore = textarea.value ? (textarea.value.trim() + ' ') : '';

            recognition.onstart = () => {
                isRecording = true;
                btnRecord.classList.add('recording');
                btnRecordLabel.textContent = 'Merekam Suara...';
                textWrapper.classList.add('recording');
                banner.style.display = 'flex';
                startTimer();
            };

            recognition.onresult = (event) => {
                let interimTranscript = '';
                let finalTranscript = '';

                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript;
                    } else {
                        interimTranscript += event.results[i][0].transcript;
                    }
                }

                if (finalTranscript) {
                    finalTranscriptBefore += finalTranscript + ' ';
                }

                textarea.value = (finalTranscriptBefore + interimTranscript).trim();
                updateState();
            };

            recognition.onerror = (event) => {
                console.warn('Speech recognition event:', event.error);
                if (event.error === 'not-allowed') {
                    alert('Izin akses mikrofon ditolak. Silakan izinkan akses mikrofon di browser untuk merekam suara.');
                }
                stopRecording();
            };

            recognition.onend = () => {
                if (isRecording) {
                    stopRecording();
                }
            };

            recognition.start();
            return true;
        } catch (e) {
            console.error('Error starting recognition:', e);
            stopRecording();
            return false;
        }
    }

    function startTimer() {
        secondsElapsed = 0;
        timerEl.textContent = '00:00';
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            secondsElapsed++;
            const mins = String(Math.floor(secondsElapsed / 60)).padStart(2, '0');
            const secs = String(secondsElapsed % 60).padStart(2, '0');
            timerEl.textContent = `${mins}:${secs}`;
        }, 1000);
    }

    function stopRecording() {
        isRecording = false;
        clearInterval(timerInterval);
        if (recognition) {
            try { recognition.stop(); } catch(e){}
            recognition = null;
        }
        btnRecord.classList.remove('recording');
        btnRecordLabel.textContent = 'Rekam Suara Anak';
        textWrapper.classList.remove('recording');
        banner.style.display = 'none';
        updateState();
    }

    // Toggle Record Button
    btnRecord.addEventListener('click', (e) => {
        e.preventDefault();
        if (isRecording) {
            stopRecording();
        } else {
            if (isSpeaking) stopSpeaking();
            startSpeechRecognition();
        }
    });

    // Stop Button in Banner
    btnStop.addEventListener('click', (e) => {
        e.preventDefault();
        stopRecording();
    });

    // 3. Text to Speech Playback
    function playTextToSpeech() {
        if (!currentSpeechSynthesis) {
            alert('Fitur suara tidak didukung pada browser ini.');
            return;
        }

        const text = textarea.value.trim();
        if (!text) return;

        currentSpeechSynthesis.cancel();
        activeUtterance = new SpeechSynthesisUtterance(text);
        activeUtterance.lang = 'id-ID';
        activeUtterance.rate = 0.95; // Friendly pace for kids

        activeUtterance.onstart = () => {
            isSpeaking = true;
            btnPlay.classList.add('speaking');
            btnPlayLabel.textContent = 'Berhenti...';
        };

        activeUtterance.onend = () => {
            stopSpeaking();
        };

        activeUtterance.onerror = () => {
            stopSpeaking();
        };

        currentSpeechSynthesis.speak(activeUtterance);
    }

    function stopSpeaking() {
        if (currentSpeechSynthesis) {
            currentSpeechSynthesis.cancel();
        }
        isSpeaking = false;
        btnPlay.classList.remove('speaking');
        btnPlayLabel.textContent = 'Dengarkan';
    }

    btnPlay.addEventListener('click', (e) => {
        e.preventDefault();
        if (isSpeaking) {
            stopSpeaking();
        } else {
            if (isRecording) stopRecording();
            playTextToSpeech();
        }
    });

    // 4. Clear Button
    btnClear.addEventListener('click', (e) => {
        e.preventDefault();
        if (isRecording) stopRecording();
        if (isSpeaking) stopSpeaking();
        textarea.value = '';
        updateState();
        textarea.focus();
    });

    // 5. Quick Inspiration Chips
    quickChips.forEach(chip => {
        chip.addEventListener('click', (e) => {
            e.preventDefault();
            const textToInsert = chip.getAttribute('data-text');
            if (textarea.value.trim().length > 0) {
                textarea.value = textarea.value.trim() + ' ' + textToInsert;
            } else {
                textarea.value = textToInsert;
            }
            updateState();
            textarea.focus();
        });
    });
});
</script>
@endsection
