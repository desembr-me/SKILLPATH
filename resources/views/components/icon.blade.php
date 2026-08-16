@props(['name' => 'spark', 'class' => ''])
@php($key = \Illuminate\Support\Str::slug($name))
<span {{ $attributes->merge(['class' => 'ui-icon '.$class]) }} aria-hidden="true">
@switch($key)
    @case('dashboard')
    @case('grid')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
        @break

    @case('users')
    @case('co-design')
        <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        @break

    @case('user')
    @case('person')
    @case('mentor')
        <svg viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        @break

    @case('child')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4"/><path d="M6 21v-2a6 6 0 0 1 12 0v2"/><path d="M9 13a3 3 0 0 0 6 0"/></svg>
        @break

    @case('package')
    @case('courses')
    @case('box')
        <svg viewBox="0 0 24 24" fill="none"><path d="m16.5 9.4-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        @break

    @case('book')
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        @break

    @case('receipt')
    @case('order')
    @case('orders')
        <svg viewBox="0 0 24 24" fill="none"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
        @break

    @case('review')
    @case('message')
        <svg viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8M8 13h5"/></svg>
        @break

    @case('progress')
    @case('trending-up')
        <svg viewBox="0 0 24 24" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        @break

    @case('calendar')
    @case('schedule')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg>
        @break

    @case('report')
    @case('file-text')
        <svg viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        @break

    @case('certificate')
    @case('award')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
        @break

    @case('analytics')
    @case('chart')
    @case('bar-chart')
        <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
        @break

    @case('pie-chart')
        <svg viewBox="0 0 24 24" fill="none"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
        @break

    @case('recycle')
    @case('recycle-bin')
        <svg viewBox="0 0 24 24" fill="none"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        @break

    @case('search')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        @break

    @case('eye')
    @case('view')
        <svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        @break

    @case('external-link')
        <svg viewBox="0 0 24 24" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        @break

    @case('printer')
    @case('print')
        <svg viewBox="0 0 24 24" fill="none"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        @break

    @case('refresh')
    @case('restore')
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/></svg>
        @break

    @case('logout')
    @case('log-out')
        <svg viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        @break

    @case('wallet')
    @case('earnings')
    @case('money')
        <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20"/><circle cx="16" cy="14" r="1.5"/></svg>
        @break

    @case('clock')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
        @break

    @case('star')
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.083 2.823a1.05 1.05 0 0 1 1.834 0l2.553 5.172 5.707.83a1.05 1.05 0 0 1 .582 1.792l-4.13 4.025.975 5.684a1.05 1.05 0 0 1-1.523 1.107L12 18.755l-5.081 2.678a1.05 1.05 0 0 1-1.523-1.107l.975-5.684-4.13-4.025a1.05 1.05 0 0 1 .582-1.792l5.707-.83 2.553-5.172z"/></svg>
        @break

    @case('plus')
        <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        @break

    @case('edit')
        <svg viewBox="0 0 24 24" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        @break

    @case('trash')
        <svg viewBox="0 0 24 24" fill="none"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        @break

    @case('check')
        <svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
        @break

    @case('arrow-right')
        <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        @break

    @case('arrow-left')
        <svg viewBox="0 0 24 24" fill="none"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        @break

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

    @case('location')
        <svg viewBox="0 0 24 24" fill="none"><path d="M20 10c0 5.5-8 11-8 11S4 15.5 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
        @break

    @case('sessions')
        <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
        @break

    @case('credit')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M3 10h18M7 14h4"/></svg>
        @break

    @case('conflict')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v5M12 17.2v.1"/></svg>
        @break

    @case('path')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="5" cy="18" r="2"/><circle cx="19" cy="6" r="2"/><path d="M7 18c5 0 2-8 7-8h3"/><path d="m14 7 3 3-3 3"/></svg>
        @break

    @case('payment')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 9h18M7 14h3"/></svg>
        @break

    @case('heart')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 20.5S3.5 15.4 3.5 9.4A4.9 4.9 0 0 1 12 6.3a4.9 4.9 0 0 1 8.5 3.1c0 6-8.5 11.1-8.5 11.1Z"/></svg>
        @break

    @case('cart')
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.2 11.4A2 2 0 0 0 9.2 17H18a2 2 0 0 0 2-1.6L21.5 8H6"/><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="17.5" cy="20.5" r="1.3"/></svg>
        @break

    @case('mic')
    @case('voice')
        <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3M9 21h6"/></svg>
        @break

    @case('mic-off')
        <svg viewBox="0 0 24 24" fill="none"><line x1="1" y1="1" x2="23" y2="23"/><path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V6a3 3 0 0 0-5.94-.6"/><path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/><line x1="12" y1="19" x2="12" y2="22"/><line x1="8" y1="22" x2="16" y2="22"/></svg>
        @break

    @case('volume')
    @case('speaker')
    @case('sound')
        <svg viewBox="0 0 24 24" fill="none"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>
        @break

    @case('stop')
    @case('square')
        <svg viewBox="0 0 24 24" fill="none"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
        @break

    @case('avatar')
        <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><circle cx="12" cy="8.2" r="4.2"/><path d="M4.2 21c.5-5.3 3.3-7.9 7.8-7.9s7.3 2.6 7.8 7.9c0 .6-.4 1-1 1H5.2c-.6 0-1-.4-1-1Z"/></svg>
        @break

    @case('bell')
    @case('notification')
        <svg viewBox="0 0 24 24" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        @break

    @case('bank')
        <svg viewBox="0 0 24 24" fill="none"><path d="m3 9 9-7 9 7v2H3V9z"/><path d="M4 11v8M8 11v8M12 11v8M16 11v8M20 11v8M2 21h20"/></svg>
        @break

    @case('qr')
    @case('qrcode')
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="3" height="3"/><rect x="18" y="14" width="3" height="3"/><rect x="14" y="18" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/></svg>
        @break

    @case('copy')
        <svg viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        @break

    @case('shield-check')
    @case('security')
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
        @break

    @case('tag')
    @case('promo')
        <svg viewBox="0 0 24 24" fill="none"><path d="m20.59 13.41-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        @break

    @case('info')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        @break

    @case('download')
        <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        @break

    @case('alert-circle')
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        @break

    @case('excel')
    @case('spreadsheet')
    @case('file-spreadsheet')
        <svg viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13l3 4M11 13l-3 4M14 13h3M14 17h3"/></svg>
        @break

    @case('spark')
    @default
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8 13.6 8l5.2 1.6-5.2 1.6L12 16.4l-1.6-5.2-5.2-1.6L10.4 8 12 2.8Z"/><path d="m18 15 .8 2.2L21 18l-2.2.8L18 21l-.8-2.2L15 18l2.2-.8L18 15Z"/></svg>
@endswitch
</span>
