@props(['course'])
@php($slug = $course->category->slug ?? \Illuminate\Support\Str::slug($course->category->name ?? 'technology'))
<div {{ $attributes->merge(['class' => 'course-art']) }} style="--course-accent: {{ $course->accent ?: '#EDE9FE' }}">
    <span class="art-shape art-shape-a"></span>
    <span class="art-shape art-shape-b"></span>
    <span class="art-grid"></span>
    <div class="art-icon"><x-icon :name="$slug" /></div>
    <div class="art-label"><span>OFFLINE CLASS</span><b>{{ strtoupper($course->category->name) }}</b></div>
</div>
