@extends('layouts.instructor')
@section('title','Kelola '.$learningPath->title)
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Kelola Course</span>
    <h1>{{ $learningPath->title }}</h1>
</div>
<div class="content-card" style="max-width: 640px;">
    <h2>Informasi penjualan</h2>
    <form method="POST" action="{{ route('instructor.courses.update', $learningPath) }}" class="form-stack">
        @csrf
        @method('PUT')
        <label><span>Harga</span><input type="number" name="price" min="0" value="{{ old('price', $learningPath->price) }}"></label>
        <label><span>Harga promo</span><input type="number" name="sale_price" min="0" value="{{ old('sale_price', $learningPath->sale_price) }}"></label>
        <label><span>Tipe course</span>
            <select name="course_type">
                <option value="self_paced" @selected(old('course_type', $learningPath->course_type) === 'self_paced')>Video & aktivitas</option>
                <option value="live" @selected(old('course_type', $learningPath->course_type) === 'live')>Live</option>
                <option value="hybrid" @selected(old('course_type', $learningPath->course_type) === 'hybrid')>Hybrid</option>
            </select>
        </label>
        <label><span>Hasil belajar</span><textarea name="learning_outcomes" rows="5">{{ old('learning_outcomes', $learningPath->learning_outcomes) }}</textarea></label>
        <label><span>Persyaratan</span><textarea name="requirements" rows="4">{{ old('requirements', $learningPath->requirements) }}</textarea></label>

        <fieldset>
            <legend>Kategori course</legend>
            <div class="payment-form">
                @php($selectedCategories = array_map('intval', old('category_ids', $learningPath->categories->pluck('id')->all())))
                @foreach($categories as $category)
                    <label>
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array((int)$category->id, $selectedCategories, true))>
                        <span>{{ $category->icon }} {{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset>
            <legend>Tag minat untuk rekomendasi</legend>
            <p>Pilih minat yang benar-benar relevan dengan isi course agar rekomendasi co-design lebih akurat.</p>
            <div class="payment-form">
                @php($selectedInterests = array_map('intval', old('interest_ids', $learningPath->interests->pluck('id')->all())))
                @foreach($interests as $interest)
                    <label>
                        <input type="checkbox" name="interest_ids[]" value="{{ $interest->id }}" @checked(in_array((int)$interest->id, $selectedInterests, true))>
                        <span>{{ $interest->icon }} {{ $interest->name }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        @if($learningPath->finalExam)
            <hr>
            <h2>Ujian akhir & retake</h2>
            <p>Atur soal dan aturan kelulusan. Urutan soal serta pilihan jawaban akan diacak otomatis untuk setiap percobaan siswa.</p>

            <label>
                <span>Judul ujian akhir</span>
                <input type="text" name="exam_title" maxlength="160" value="{{ old('exam_title', $learningPath->finalExam->title) }}" required>
            </label>

            <div class="two-fields">
                <label><span>Nilai minimal ujian akhir</span><input type="number" name="exam_passing_score" min="50" max="100" value="{{ old('exam_passing_score', $learningPath->finalExam->passing_score) }}" required></label>
                <label><span>Maksimal percobaan ujian</span><input type="number" name="exam_max_attempts" min="1" max="10" value="{{ old('exam_max_attempts', $learningPath->finalExam->max_attempts) }}" required></label>
            </div>

            @php($examQuestions = old('exam_questions', $learningPath->finalExam->questions ?? []))
            @foreach($examQuestions as $questionIndex => $question)
                <div class="content-card">
                    <h3>Soal {{ $loop->iteration }}</h3>
                    <label>
                        <span>Pertanyaan</span>
                        <textarea name="exam_questions[{{ $questionIndex }}][question]" rows="2" maxlength="500" required>{{ $question['question'] ?? '' }}</textarea>
                    </label>

                    @foreach(($question['options'] ?? []) as $optionIndex => $option)
                        <label>
                            <span>Pilihan {{ $loop->iteration }}</span>
                            <input type="text" name="exam_questions[{{ $questionIndex }}][options][{{ $optionIndex }}]" maxlength="300" value="{{ $option }}" required>
                        </label>
                    @endforeach

                    <label>
                        <span>Jawaban benar</span>
                        <select name="exam_questions[{{ $questionIndex }}][correct]" required>
                            @foreach(($question['options'] ?? []) as $optionIndex => $option)
                                <option value="{{ $optionIndex }}" @selected((int)($question['correct'] ?? 0) === (int)$optionIndex)>Pilihan {{ $loop->iteration }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            @endforeach
        @endif

        <button class="btn btn-dark" type="submit">Simpan Course</button>
    </form>
</div>
@endsection
