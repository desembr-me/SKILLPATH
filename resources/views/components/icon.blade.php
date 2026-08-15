@props(['name' => 'spark', 'class' => ''])
@php($key = \Illuminate\Support\Str::slug($name))
<span {{ $attributes->merge(['class' => 'ui-icon '.$class]) }} aria-hidden="true">
@switch($key)
    @case('arts')
    @case('palette')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3a9 9 0 1 0 0 18h1.2a1.8 1.8 0 0 0 0-3.6h-1.1a1.5 1.5 0 0 1 0-3h2.7A6.2 6.2 0 0 0 21 8.2C21 5.3 17 3 12 3Z"/><circle cx="7.4" cy="9" r="1"/><circle cx="10.2" cy="6.5" r="1"/><circle cx="14" cy="6.4" r="1"/><circle cx="17" cy="9" r="1"/></svg>
        @break
    @case('music')
        <svg viewBox="0 0 24 24" fill="none"><path d="M9 18V6.8l10-2.2v10.7"/><path d="M9 9.2 19 7"/><circle cx="6.5" cy="18" r="2.5"/><circle cx="16.5" cy="15.5" r="2.5"/></svg>
        @break
    @case('languages')
    @case('language')
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 5h9v7H8l-4 3V5Z"/><path d="M11 10h9v8h-4l-4 3v-3h-1"/><path d="m6.5 8.5 1.2 1.2 2-2.3"/></svg>
        @break
    @case('sports')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="m8.5 4.8 2.4 3.1-1.2 3.7-3.8 1.2-2.7-2"/><path d="m15.5 4.8-2.4 3.1 1.2 3.7 3.8 1.2 2.7-2"/><path d="m9.7 11.6 2.3 2 2.3-2M12 13.6v4.2M8.4 19.2 12 17.8l3.6 1.4"/></svg>
        @break
    @case('self-improvement')
    @case('growth')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 21v-9"/><path d="M12 15c-4.8 0-7-2.5-7-6.5 4.8 0 7 2.5 7 6.5Z"/><path d="M12 12c4.8 0 7-2.5 7-6.5-4.8 0-7 2.5-7 6.5Z"/><path d="M8 21h8"/></svg>
        @break
    @case('technology')
    @case('code')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="m9 9-3 3 3 3M15 9l3 3-3 3M13 8l-2 8"/></svg>
        @break
    @case('calendar')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 10h18"/><path d="M7 14h2M11 14h2M15 14h2M7 17h2M11 17h2"/></svg>
        @break
    @case('location')
        <svg viewBox="0 0 24 24" fill="none"><path d="M20 10c0 5.5-8 11-8 11S4 15.5 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
        @break
    @case('sessions')
        <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
        @break
    @case('clock')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        @break
    @case('star')
        <svg viewBox="0 0 24 24" fill="none"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9L12 3Z"/></svg>
        @break
    @case('credit')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M3 10h18M7 14h4"/></svg>
        @break
    @case('conflict')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v5M12 17.2v.1"/></svg>
        @break
    @case('certificate')
        <svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v14H6z"/><path d="M9 7h6M9 10h6"/><circle cx="12" cy="14" r="2"/><path d="m10.8 15.6-1 5 2.2-1.3 2.2 1.3-1-5"/></svg>
        @break
    @case('review')
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 4h16v12H9l-5 4V4Z"/><path d="m8 10 2 2 5-5"/></svg>
        @break
    @case('co-design')
    @case('users')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.3"/><path d="M3.5 19c.3-4 2.5-6 5.5-6s5.2 2 5.5 6M14 14c3.7-.6 6.2 1 6.5 4.5"/></svg>
        @break
    @case('path')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="5" cy="18" r="2"/><circle cx="19" cy="6" r="2"/><path d="M7 18c5 0 2-8 7-8h3"/><path d="m14 7 3 3-3 3"/></svg>
        @break
    @case('child')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4"/><path d="M5 21c.4-5 3-7.5 7-7.5s6.6 2.5 7 7.5"/><path d="M8 7c1.3-2.2 5-3.2 8-1"/></svg>
        @break
    @case('payment')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 9h18M7 14h3"/></svg>
        @break
    @case('check')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
        @break
    @case('arrow-right')
        <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M14 7l5 5-5 5"/></svg>
        @break
    @case('arrow-left')
        <svg viewBox="0 0 24 24" fill="none"><path d="M19 12H5M10 7l-5 5 5 5"/></svg>
        @break
    @case('heart')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 20.5S3.5 15.4 3.5 9.4A4.9 4.9 0 0 1 12 6.3a4.9 4.9 0 0 1 8.5 3.1c0 6-8.5 11.1-8.5 11.1Z"/></svg>
        @break
    @case('cart')
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.2 11.4A2 2 0 0 0 9.2 17H18a2 2 0 0 0 2-1.6L21.5 8H6"/><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="17.5" cy="20.5" r="1.3"/></svg>
        @break
    @case('book')
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15.5H6.5A2.5 2.5 0 0 0 4 21V5.5Z"/><path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H20"/></svg>
        @break
    @case('receipt')
        <svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v18l-2.5-1.5L13 21l-1-1.5L11 21l-2.5-1.5L6 21V3Z"/><path d="M9 8h6M9 12h6"/></svg>
        @break
    @case('mic')
    @case('voice')
        <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3M9 21h6"/></svg>
        @break
    @case('spark')
    @default
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8 13.6 8l5.2 1.6-5.2 1.6L12 16.4l-1.6-5.2-5.2-1.6L10.4 8 12 2.8Z"/><path d="m18 15 .8 2.2L21 18l-2.2.8L18 21l-.8-2.2L15 18l2.2-.8L18 15Z"/></svg>
@endswitch
</span>
