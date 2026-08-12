@extends('layouts.app')

@section('title', $learningPath->title.' | SKILLPATH')

@section('content')
<section class="path-hero">
    <div class="container path-hero-grid">
        <div>
            <a class="back-link" href="{{ route('dashboard') }}">← Kembali ke dashboard</a>
            <div class="path-title-row">
                <div class="path-icon large">{{ $learningPath->icon }}</div>
                <div>
                    <span class="eyebrow">{{ $learningPath->skill->name }}</span>
                    <h1>{{ $learningPath->title }}</h1>
                </div>
            </div>
            <p>{{ $learningPath->description }}</p>

            <div class="path-meta big">
                <span>Usia {{ $learningPath->min_age }}–{{ $learningPath->max_age }}</span>
                <span>{{ $learningPath->level }}</span>
                <span>± {{ $learningPath->duration_minutes }} menit</span>
            </div>
        </div>

        <div class="path-progress-panel">
            <span>Progres jalur</span>
            <strong>{{ $progressPercent }}%</strong>
            <div class="progress-track large">
                <span style="width: {{ $progressPercent }}%"></span>
            </div>
            @if ($nextActivity)
                <p>Aktivitas berikutnya: <strong>{{ $nextActivity->title }}</strong></p>
            @else
                <p>Semua aktivitas sudah selesai.</p>
            @endif
        </div>
    </div>
</section>

<section class="section">
    <div class="container learning-layout">
        <div class="module-list">
            <div class="section-heading">
                <span class="eyebrow">Isi jalur</span>
                <h2>Modul belajar</h2>
            </div>

            @foreach ($learningPath->modules as $module)
                @php
                    $done = $module->activities->whereIn('id', $completedIds)->count();
                    $total = $module->activities->count();
                @endphp

                <article class="module-card">
                    <div class="module-index">{{ $loop->iteration }}</div>
                    <div class="module-content">
                        <span class="module-progress">{{ $done }}/{{ $total }} aktivitas selesai</span>
                        <h3>{{ $module->title }}</h3>
                        <p>{{ $module->summary }}</p>
                        <a class="card-link" href="{{ route('learning.module', $module) }}">Buka modul →</a>
                    </div>
                </article>
            @endforeach
        </div>

        <aside class="learning-aside">
            <div class="aside-card">
                <h3>Minat terkait</h3>
                <div class="interest-chip-row">
                    @foreach ($learningPath->interests as $interest)
                        <span class="interest-chip">{{ $interest->icon }} {{ $interest->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="aside-card tip-card">
                <span class="tip-icon">i</span>
                <h3>Tips belajar</h3>
                <p>Kerjakan satu aktivitas sampai selesai. Beri waktu untuk mencoba sebelum melihat bantuan dari orang tua.</p>
            </div>
        </aside>
    </div>
</section>
@endsection
