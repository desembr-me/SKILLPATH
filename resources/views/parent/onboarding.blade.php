@extends('layouts.app')
@section('title','Co-Design Minat Anak')
@section('content')
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
            <div class="voice-input-container" id="childVoiceContainer">
                <div class="voice-input-header">
                    <span class="voice-label">Apa kata anak tentang pilihannya? (opsional)</span>
                    <div class="voice-actions">
                        <button type="button" class="btn-voice-speak" id="btnVoiceSpeak" style="display:none;" title="Dengarkan kembali kata-kata anak">
                            <x-icon name="volume" /> <span>Dengarkan</span>
                        </button>
                        <button type="button" class="btn-voice-record" id="btnVoiceRecord" title="Rekam suara anak untuk diubah menjadi teks">
                            <x-icon name="mic" id="voiceMicIcon" /> <span id="voiceRecordText">Bicara / Rekam Suara</span>
                        </button>
                        <button type="button" class="btn-voice-clear" id="btnVoiceClear" style="display:none;" title="Hapus teks">
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="voice-status-banner" id="voiceStatusBanner">
                    <div class="voice-status-waves">
                        <span></span><span></span><span></span>
                    </div>
                    <span id="voiceStatusMsg">Mendengarkan suara anak... Silakan berbicara sekarang.</span>
                </div>

                <div class="voice-textarea-box" id="voiceTextareaBox">
                    <textarea name="child_voice" id="childVoiceTextarea" rows="3" placeholder="Tuliskan dengan kata-kata anak sendiri, atau klik tombol 'Bicara / Rekam Suara' di atas...">{{ old('child_voice') }}</textarea>
                </div>
                <p class="voice-hint-text">💡 Tips: Klik tombol <b>Bicara / Rekam Suara</b> agar anak dapat langsung menyampaikan alasannya secara verbal.</p>
            </div>
            <label class="confirm-check"><input type="checkbox" name="discussed_with_child" value="1" required @checked(old('discussed_with_child'))> <span>Saya (orang tua) sudah mendiskusikan pilihan ini bersama anak, bukan memutuskan sendiri.</span></label>
            <div class="onboarding-submit"><p>Jawaban anak digunakan untuk membuat rekomendasi awal. Orang tua tetap dapat memilih course yang paling sesuai.</p><button class="btn btn-primary btn-lg">Buat Jalur Belajar <x-icon name="arrow-right" /></button></div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recordBtn = document.getElementById('btnVoiceRecord');
    const speakBtn = document.getElementById('btnVoiceSpeak');
    const clearBtn = document.getElementById('btnVoiceClear');
    const textarea = document.getElementById('childVoiceTextarea');
    const banner = document.getElementById('voiceStatusBanner');
    const statusMsg = document.getElementById('voiceStatusMsg');
    const textareaBox = document.getElementById('voiceTextareaBox');
    const recordText = document.getElementById('voiceRecordText');

    if (!recordBtn || !textarea) return;

    // Check existing content for actions visibility
    function updateActionButtons() {
        const hasText = textarea.value.trim().length > 0;
        if (speakBtn) speakBtn.style.display = hasText ? 'inline-flex' : 'none';
        if (clearBtn) clearBtn.style.display = hasText ? 'inline-block' : 'none';
    }

    textarea.addEventListener('input', updateActionButtons);
    updateActionButtons();

    // Clear Text
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            textarea.value = '';
            updateActionButtons();
            textarea.focus();
        });
    }

    // Speech Recognition setup
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    let isRecording = false;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'id-ID';

        let initialText = '';

        recognition.onstart = function() {
            isRecording = true;
            initialText = textarea.value ? textarea.value.trim() + ' ' : '';
            recordBtn.classList.add('recording');
            recordText.textContent = 'Berhenti Merekam';
            banner.classList.add('active');
            statusMsg.textContent = 'Mendengarkan suara anak... Silakan berbicara.';
            textareaBox.classList.add('recording');
        };

        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }

            const current = (finalTranscript || interimTranscript).trim();
            if (current) {
                textarea.value = initialText + current;
                updateActionButtons();
            }
        };

        recognition.onerror = function(event) {
            console.warn('Speech recognition error:', event.error);
            if (event.error === 'not-allowed') {
                alert('Akses mikrofon tidak diizinkan. Silakan beri izin mikrofon pada browser Anda untuk menggunakan fitur suara.');
            }
            stopRecording();
        };

        recognition.onend = function() {
            stopRecording();
        };
    }

    function stopRecording() {
        isRecording = false;
        if (recordBtn) {
            recordBtn.classList.remove('recording');
            recordText.textContent = 'Bicara / Rekam Suara';
        }
        if (banner) banner.classList.remove('active');
        if (textareaBox) textareaBox.classList.remove('recording');
    }

    recordBtn.addEventListener('click', function() {
        if (!SpeechRecognition) {
            alert('Fitur pengenalan suara otomatis belum didukung pada peramban ini. Anda tetap dapat mengetik suara anak secara manual.');
            return;
        }

        if (isRecording) {
            recognition.stop();
            stopRecording();
        } else {
            try {
                recognition.start();
            } catch (err) {
                console.error(err);
                recognition.stop();
            }
        }
    });

    // Text to Speech playback
    if (speakBtn && ('speechSynthesis' in window)) {
        let isSpeaking = false;

        speakBtn.addEventListener('click', function() {
            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                speakBtn.classList.remove('speaking');
                speakBtn.querySelector('span').textContent = 'Dengarkan';
                return;
            }

            const textToSpeak = textarea.value.trim();
            if (!textToSpeak) return;

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(textToSpeak);
            utterance.lang = 'id-ID';
            utterance.rate = 0.95;

            // Attempt to pick an Indonesian voice if available
            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID'));
            if (idVoice) utterance.voice = idVoice;

            utterance.onstart = function() {
                isSpeaking = true;
                speakBtn.classList.add('speaking');
                speakBtn.querySelector('span').textContent = 'Memutar Suara...';
            };

            utterance.onend = function() {
                isSpeaking = false;
                speakBtn.classList.remove('speaking');
                speakBtn.querySelector('span').textContent = 'Dengarkan';
            };

            utterance.onerror = function() {
                isSpeaking = false;
                speakBtn.classList.remove('speaking');
                speakBtn.querySelector('span').textContent = 'Dengarkan';
            };

            window.speechSynthesis.speak(utterance);
        });
    }
});
</script>
@endpush
@endsection
