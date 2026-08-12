@extends('layouts.app')

@section('title', 'Dashboard | SKILLPATH')

@section('content')
<section class="dashboard-hero">
    <div class="container dashboard-hero-grid">
        <div>
            <span class="eyebrow">Halo, {{ $child->name }}</span>
            <h1>Mau belajar apa hari ini?</h1>
            <p>Rekomendasi di bawah disusun dari usia, minat, dan progresmu.</p>
        </div>

        <a class="btn btn-ghost-light" href="{{ route('onboarding.edit') }}">Ubah Minat</a>
    </div>
</section>

<section class="section dashboard-section">
    <div class="container">
        <div class="stat-grid">
            <article class="stat-card">
                <span>Aktivitas selesai</span>
                <strong>{{ $completedActivities }}</strong>
            </article>
            <article class="stat-card">
                <span>Total poin</span>
                <strong>{{ $totalPoints }}</strong>
            </article>
            <article class="stat-card">
                <span>Minat aktif</span>
                <strong>{{ $child->interests->count() }}</strong>
            </article>
        </div>

        <div class="section-heading split-heading dashboard-heading">
            <div>
                <span class="eyebrow">Direkomendasikan untukmu</span>
                <h2>Jalur berikutnya</h2>
            </div>
            <div class="interest-chip-row">
                @foreach ($child->interests as $interest)
                    <span class="interest-chip">{{ $interest->icon }} {{ $interest->name }}</span>
                @endforeach
            </div>
        </div>

        <div class="path-grid">
            @forelse ($recommendations as $item)
                @php($path = $item['path'])
                <article class="path-card recommendation-card">
                    <div class="recommendation-top">
                        <div class="path-icon">{{ $path->icon }}</div>
                        <span class="match-badge">
                            {{ $item['matched_interests'] > 0 ? 'Sesuai minat' : 'Pilihan baru' }}
                        </span>
                    </div>

                    <span class="path-skill">{{ $path->skill->name }}</span>
                    <h3>{{ $path->title }}</h3>
                    <p>{{ $path->description }}</p>

                    <div class="progress-line">
                        <div>
                            <span>Progres</span>
                            <strong>{{ $item['progress_percent'] }}%</strong>
                        </div>
                        <div class="progress-track">
                            <span style="width: {{ $item['progress_percent'] }}%"></span>
                        </div>
                    </div>

                    <a class="btn btn-dark btn-full" href="{{ route('learning.path', $path) }}">
                        {{ $item['progress_percent'] > 0 ? 'Lanjutkan Jalur' : 'Mulai Jalur' }}
                    </a>
                </article>
            @empty
                <article class="empty-card">
                    <h3>Belum ada jalur yang cocok.</h3>
                    <p>Ubah minat atau periksa rentang usia pada data jalur belajar.</p>
                </article>
            @endforelse
        </div>
    </div>
</section>
@endsection
