@props(['mentor'])
<div class="mentor-flip">
    <div class="mentor-flip-inner">
        <div class="mentor-flip-face mentor-flip-front" style="--flip-accent: {{ $mentor->category->accent ?? '#EAE5FF' }}">
            <div class="mentor-flip-photo">
                @if($mentor->avatar_url)
                    <img src="{{ $mentor->avatar_url }}" alt="Foto {{ $mentor->name }}">
                @else
                    <x-icon name="avatar" />
                @endif
            </div>
            <div class="mentor-flip-name">
                <h3>{{ $mentor->name }} <span class="mentor-verified-badge" title="Mentor Terverifikasi SkillPath"><x-icon name="verified" /></span></h3>
                @if($mentor->category)<span class="mentor-flip-tag">{{ $mentor->category->name }}</span>@endif
            </div>
            <span class="mentor-flip-hint"><x-icon name="path" /> Sentuh untuk lihat detail</span>
        </div>
        <div class="mentor-flip-face mentor-flip-back">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:6px; margin-bottom:4px;">
                <h3 style="margin:0;">{{ $mentor->name }}</h3>
                <span class="mentor-verified-badge" title="Mentor Terverifikasi SkillPath"><x-icon name="verified" /></span>
            </div>
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
