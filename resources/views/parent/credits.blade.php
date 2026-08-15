@extends('layouts.app')
@section('title','Kredit Sesi')
@section('content')
<x-parent-nav />
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Sistem Kredit Sesi Fleksibel</span><h1>Kredit Sesi</h1><p>Gunakan kredit untuk memilih sesi pengganti tanpa proses refund manual.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    <div class="info-banner"><span><x-icon name="credit" /></span><div><b>Bagaimana kredit bekerja?</b><p>Kredit dibuat untuk sesi yang memenuhi kebijakan, misalnya sakit atau perubahan jadwal yang disetujui. Kredit memiliki masa berlaku dan dapat digunakan pada sesi pengganti yang tersedia.</p></div></div>
    <div class="panel">
        @forelse($credits as $credit)
            <div class="credit-row">
                <div class="credit-code-mark"><x-icon name="credit" /></div>
                <div><span class="status-chip {{ $credit->status }}">{{ ucfirst($credit->status) }}</span><h3>{{ $credit->credit_code }}</h3><p>{{ $credit->child->name }} • {{ $credit->enrollment->course->title }} • {{ $credit->reason }}</p><small>Berlaku sampai {{ $credit->expires_at->format('d M Y') }}</small></div>
                @if($credit->status === 'available')
                    <form method="POST" action="{{ route('parent.credits.redeem',$credit) }}" class="inline-form">@csrf
                        <select name="course_session_id" required><option value="">Pilih sesi pengganti</option>@foreach($credit->enrollment->schedule->sessions->where('session_date','>=',now()->startOfDay()) as $session)<option value="{{ $session->id }}">Sesi {{ $session->session_no }} • {{ $session->session_date->format('d M Y') }} • {{ substr($session->start_time,0,5) }}</option>@endforeach</select>
                        <button class="btn btn-primary">Gunakan Kredit</button>
                    </form>
                @else<span class="status-chip {{ $credit->status }}">{{ ucfirst($credit->status) }}</span>@endif
            </div>
        @empty<div class="empty-state"><x-icon name="credit" /><div><b>Belum ada kredit sesi</b><span>Kredit akan muncul ketika ada sesi yang memenuhi kebijakan konversi.</span></div></div>@endforelse
    </div>
</section>
@endsection
