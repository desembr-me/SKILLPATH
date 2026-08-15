@props(['mentor'])
<div class="mentor-flip">
    <div class="mentor-flip-inner">
        <div class="mentor-flip-face mentor-flip-front" style="--flip-accent: {{ $mentor->category->accent ?? '#EAE5FF' }}">
            <div class="mentor-flip-photo">
                @if($mentor->avatar)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($mentor->avatar) }}" alt="Foto {{ $mentor->name }}">
                @else
                    <x-icon name="avatar" />
                @endif
            </div>
            <div class="mentor-flip-name">
                <h3>{{ $mentor->name }}</h3>
                @if($mentor->category)<span class="mentor-flip-tag">{{ $mentor->category->name }}</span>@endif
            </div>
            <span class="mentor-flip-hint"><x-icon name="path" /> Sentuh untuk lihat detail</span>
        </div>
        <div class="mentor-flip-face mentor-flip-back">
            <h3>{{ $mentor->name }}</h3>
            <span class="mentor-flip-headline">{{ $mentor->headline ?: 'Mentor SkillPath' }}</span>
            <p class="mentor-flip-bio">{{ $mentor->bio ?: 'Mentor berpengalaman di SkillPath, siap mendampingi anak belajar dengan cara yang menyenangkan.' }}</p>
            <div class="mini-tags">
                <span>{{ $mentor->courses->count() }} course diajar</span>
                <span>Rating {{ $mentor->rating ?: '0.0' }}</span>
            </div>
            <a class="btn btn-primary btn-sm" href="{{ route('mentors.show', $mentor) }}">Lihat Profil <x-icon name="arrow-right" /></a>
        </div>
    </div>
</div>
