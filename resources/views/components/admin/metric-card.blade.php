@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'blue',
])

<article {{ $attributes->class(['admin-metric-card', 'tone-'.$tone]) }}>
    <div class="admin-metric-top">
        <span>{{ $label }}</span>
        <i aria-hidden="true"></i>
    </div>

    <strong>{{ $value }}</strong>

    @if($hint)
        <small>{{ $hint }}</small>
    @endif
</article>
