@extends('layouts.app')
@section('title','Belajar - '.$enrollment->course->title)
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Belajar</span>
            <h1>{{ $enrollment->course->title }}</h1>
            <p>{{ $enrollment->child->name }} • Jalur belajar, modul, dan aktivitas course ini.</p>
        </div>
    </div>

    @if(!in_array($enrollment->status,['active','completed']))
        <div class="info-banner"><span><x-icon name="conflict" /></span><div><b>Menunggu pembayaran</b><p>Modul dan aktivitas terbuka setelah pembayaran course ini selesai.</p></div></div>
    @else
        <div class="info-banner"><span><x-icon name="path" /></span><div><b>{{ $enrollment->progress }}% aktivitas selesai</b><p>Progres dicatat oleh mentor berdasarkan aktivitas yang telah diselesaikan bersama anak.</p></div></div>

        <div class="module-list">
            @foreach($enrollment->course->modules as $module)
            <div class="panel module-card">
                <div class="panel-heading"><div><span class="panel-kicker">Modul {{ $module->sequence }}</span><h2>{{ $module->title }}</h2></div></div>
                @if($module->description)<p class="module-desc">{{ $module->description }}</p>@endif
                @foreach($module->activities as $activity)
                    @php($done = in_array($activity->id,$completedIds))
                    <div class="activity-row">
                        <span class="activity-type-mark {{ $activity->type }}"><x-icon :name="$activity->type==='materi' ? 'book' : ($activity->type==='latihan' ? 'check' : 'review')" /></span>
                        <div><b>{{ $activity->title }}</b><small>{{ ucfirst($activity->type) }}</small></div>
                        <span class="status-chip {{ $done ? 'paid' : 'locked' }}">{{ $done ? 'Selesai' : 'Belum selesai' }}</span>
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
