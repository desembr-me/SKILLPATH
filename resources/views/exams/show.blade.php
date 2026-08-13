@extends('layouts.app')
@section('title','Ujian Akhir | '.$learningPath->title)
@section('content')
<section class="simple-hero">
    <div class="container">
        <span class="eyebrow">Ujian Akhir</span>
        <h1>{{ $learningPath->finalExam->title }}</h1>
        <p>Nilai lulus {{ $learningPath->finalExam->passing_score }} · Maksimal {{ $learningPath->finalExam->max_attempts }} percobaan.</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <div class="content-card">
            <h2>Status kelulusan</h2>
            @if($evaluation['exam_passed'])
                <div class="done-label">✓ Ujian lulus dengan nilai {{ number_format($evaluation['exam_score'],0) }}</div>
                @if($certificate)
                    <p><a class="btn btn-dark" href="{{ route('certificates.show',$learningPath) }}">Lihat Sertifikat</a></p>
                @endif
            @else
                <p>Percobaan digunakan: <strong>{{ $evaluation['attempts_used'] }}/{{ $evaluation['max_attempts'] }}</strong> · Sisa retake: <strong>{{ $evaluation['attempts_remaining'] }}</strong></p>
                @if($bestAttempt)
                    <div class="schedule-row">
                        <div>
                            <strong>Nilai terbaik</strong>
                            <span>{{ number_format((float)$bestAttempt->score,0) }} · Percobaan ke-{{ $bestAttempt->attempt_number }}</span>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        @if($attempts->isNotEmpty())
            <div class="content-card">
                <h2>Riwayat percobaan</h2>
                @foreach($attempts as $attempt)
                    <div class="schedule-row">
                        <div>
                            <strong>Percobaan ke-{{ $attempt->attempt_number }}</strong>
                            <span>
                                Nilai {{ number_format((float)$attempt->score,0) }}
                                · {{ $attempt->passed ? 'Lulus' : 'Belum lulus' }}
                                @if($attempt->correct_answers !== null && $attempt->question_count)
                                    · {{ $attempt->correct_answers }}/{{ $attempt->question_count }} benar
                                @endif
                                · {{ $attempt->completed_at?->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <span>{{ $attempt->passing_score_snapshot ? 'Batas '.$attempt->passing_score_snapshot : 'Batas '.$learningPath->finalExam->passing_score }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if(!$evaluation['exam_passed'] && $evaluation['attempts_remaining'] > 0)
            <form method="POST" action="{{ route('exams.submit',$learningPath) }}" class="content-card form-stack">
                @csrf
                <input type="hidden" name="exam_version" value="{{ $examVersion }}">
                <h2>{{ $evaluation['attempts_used'] > 0 ? 'Retake Ujian Akhir' : 'Mulai Ujian Akhir' }}</h2>
                <p>Jawab seluruh pertanyaan. Urutan soal dan pilihan diacak untuk setiap percobaan. Sertifikat baru diterbitkan jika nilai mencapai batas kelulusan.</p>

                @foreach($examPresentation as $index => $question)
                    <fieldset>
                        <legend>{{ $loop->iteration }}. {{ $question['question'] }}</legend>
                        <div class="payment-form">
                            @foreach($question['options'] as $optionIndex => $option)
                                <label>
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $optionIndex }}" required>
                                    <span>{{ $option['text'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                <button class="btn btn-dark btn-full" type="submit">Kirim Jawaban Percobaan ke-{{ $nextAttemptNumber }}</button>
            </form>
        @elseif(!$evaluation['exam_passed'])
            <div class="content-card">
                <h2>Batas percobaan tercapai</h2>
                <p>Seluruh kesempatan ujian telah digunakan. Pengajar dapat meninjau hasil dan menambah batas percobaan jika memang diperlukan.</p>
            </div>
        @endif
    </div>
</section>
@endsection
