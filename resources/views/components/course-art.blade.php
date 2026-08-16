@props(['course' => null])
@php
    $category = $course?->category;
    $slug = $category?->slug ?? \Illuminate\Support\Str::slug($category?->name ?? 'technology');
    $accent = $course?->accent ?: '#EDE9FE';
    $coverImage = $course?->cover_image;
@endphp
<div {{ $attributes->merge(['class' => 'course-art'.($coverImage ? ' has-photo' : '')]) }} style="--course-accent: {{ $accent }}">
    @if($coverImage)
        <img class="course-photo" src="{{ asset('foto/'.rawurlencode($coverImage)) }}" alt="{{ $course?->title ?? 'Course' }}" loading="lazy">
    @else
        <span class="art-shape art-shape-a"></span>
        <span class="art-shape art-shape-b"></span>
        <span class="art-grid"></span>
        <div class="art-icon"><x-icon :name="$slug" /></div>
    @endif
    <div class="art-label"><span>OFFLINE CLASS</span><b>{{ strtoupper($category?->name ?? 'KURSUS') }}</b></div>
</div>
