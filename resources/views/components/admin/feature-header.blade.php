@props([
    'eyebrow',
    'title',
    'description' => null,
])

<section {{ $attributes->class(['admin-feature-header']) }}>
    <div class="admin-feature-copy">
        <span class="admin-eyebrow">{{ $eyebrow }}</span>
        <h2>{{ $title }}</h2>

        @if($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="admin-feature-actions">
            {{ $actions }}
        </div>
    @endisset
</section>
