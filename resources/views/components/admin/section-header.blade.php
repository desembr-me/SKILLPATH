@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<header {{ $attributes->class(['admin-section-header']) }}>
    <div>
        @if($eyebrow)
            <span class="admin-eyebrow">{{ $eyebrow }}</span>
        @endif

        <h2>{{ $title }}</h2>

        @if($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="admin-section-actions">
            {{ $actions }}
        </div>
    @endisset
</header>
