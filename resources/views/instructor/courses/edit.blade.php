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
                <option value="self_paced" @selected($learningPath->course_type === 'self_paced')>Video & aktivitas</option>
                <option value="live" @selected($learningPath->course_type === 'live')>Live</option>
                <option value="hybrid" @selected($learningPath->course_type === 'hybrid')>Hybrid</option>
            </select>
        </label>
        <label><span>Hasil belajar</span><textarea name="learning_outcomes" rows="5">{{ old('learning_outcomes', $learningPath->learning_outcomes) }}</textarea></label>
        <label><span>Persyaratan</span><textarea name="requirements" rows="4">{{ old('requirements', $learningPath->requirements) }}</textarea></label>
        <button class="btn btn-dark" type="submit">Simpan Course</button>
    </form>
</div>
@endsection
