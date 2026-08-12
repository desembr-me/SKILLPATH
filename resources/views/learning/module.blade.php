@extends('layouts.app')

@section('title', $module->title.' | SKILLPATH')

@section('content')
<section class="module-hero">
    <div class="container narrow">
        <a class="back-link" href="{{ route('learning.path', $module->learningPath) }}">← Kembali ke jalur</a>
        <span class="eyebrow">{{ $module->learningPath->title }}</span>
        <h1>{{ $module->title }}</h1>
        <p>{{ $module->summary }}</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <div class="activity-stack">
            @foreach ($module->activities as $activity)
                @php($isCompleted = $completedIds->contains($activity->id))

                <article class="activity-card {{ $isCompleted ? 'completed' : '' }}">
                    <div class="activity-top">
                        <span class="activity-type">{{ strtoupper($activity->type) }}</span>
                        <span class="activity-points">+{{ $activity->points }} poin</span>
                    </div>

                    <h2>{{ $loop->iteration }}. {{ $activity->title }}</h2>
                    <p>{{ $activity->instructions }}</p>

                    @if ($isCompleted)
                        <div class="done-label">✓ Sudah selesai</div>
                    @else
                        <form method="POST" action="{{ route('learning.activity.complete', $activity) }}">
                            @csrf
                            <button class="btn btn-dark" type="submit">Tandai Selesai</button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
