@extends('layouts.app')
@section('title',$learningPath->title.' | SKILLPATH')
@section('content')
<section class="course-detail-hero"><div class="container course-detail-grid">
<div>
<div class="course-category-line">@foreach($learningPath->categories as $c)<a href="{{ route('explore.index',['category'=>$c->slug]) }}">{{ $c->name }}</a>@endforeach</div>
<h1>{{ $learningPath->title }}</h1><p class="course-lead">{{ $learningPath->description }}</p>
<div class="rating-line big"><strong>{{ number_format($averageRating,1) }}</strong><span>★</span><small>{{ $reviewCount }} ulasan</small><small>{{ $studentCount }} peserta</small><small>Usia {{ $learningPath->min_age }}–{{ $learningPath->max_age }}</small></div>
@if($learningPath->instructor)<a class="teacher-mini" href="{{ route('instructors.show',$learningPath->instructor) }}"><span class="teacher-avatar">{{ strtoupper(substr($learningPath->instructor->name,0,1)) }}</span><span><small>Pengajar</small><strong>{{ $learningPath->instructor->name }}</strong></span></a>@endif
</div>
<aside class="purchase-card">
<div class="course-thumb large"><span>{{ $learningPath->icon }}</span><small>{{ strtoupper(str_replace('_',' ',$learningPath->course_type)) }}</small></div>
@if($learningPath->is_free)<div class="purchase-price"><strong>Gratis</strong></div>@else<div class="purchase-price">@if($learningPath->sale_price)<del>Rp{{ number_format($learningPath->price,0,',','.') }}</del><span class="discount-badge">-{{ $learningPath->discountPercent() }}%</span>@endif<strong>Rp{{ number_format($learningPath->effectivePrice(),0,',','.') }}</strong></div>@endif
@auth
@if(auth()->user()->role==='parent')
@if($isEnrolled)<a class="btn btn-dark btn-full" href="{{ route('learning.path',$learningPath) }}">Lanjut Belajar</a>
@elseif($learningPath->is_free)<form method="POST" action="{{ route('courses.enroll-free',$learningPath) }}">@csrf<button class="btn btn-blue btn-full" type="submit">Aktifkan Gratis</button></form>
@else<form method="POST" action="{{ route('cart.add',$learningPath) }}">@csrf<button class="btn btn-dark btn-full" type="submit">Tambah ke Keranjang</button></form>@endif
<form method="POST" action="{{ route('wishlist.toggle',$learningPath) }}" class="mt-10">@csrf<button class="btn btn-ghost btn-full" type="submit">{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}</button></form>
@endif
@else<a class="btn btn-dark btn-full" href="{{ route('register') }}">Daftar untuk Membeli</a>@endauth
<ul class="purchase-features"><li>✓ {{ $learningPath->modules->count() }} modul</li><li>✓ {{ $learningPath->duration_minutes }} menit materi inti</li><li>✓ Akses {{ $learningPath->access_days ? $learningPath->access_days.' hari' : 'tanpa batas' }}</li><li>✓ {{ $learningPath->certificate_enabled ? 'Sertifikat penyelesaian' : 'Tanpa sertifikat' }}</li><li>✓ {{ $learningPath->live_class_enabled ? 'Live class tersedia' : 'Belajar mandiri' }}</li></ul>
</aside></div></section>
<section class="section"><div class="container course-content-grid"><div>
<div class="content-card"><h2>Yang akan dipelajari</h2><p>{{ $learningPath->learning_outcomes ?: 'Anak akan menyelesaikan aktivitas bertahap yang dirancang untuk mengembangkan skill melalui praktik.' }}</p></div>
<div class="content-card"><h2>Kurikulum course</h2>@foreach($learningPath->modules as $module)<details class="curriculum-item" @if($loop->first) open @endif><summary><strong>{{ $loop->iteration }}. {{ $module->title }}</strong><span>{{ $module->activities->count() }} aktivitas</span></summary><p>{{ $module->summary }}</p><ul>@foreach($module->activities as $a)<li>{{ $a->title }} <small>{{ strtoupper($a->type) }}</small></li>@endforeach</ul></details>@endforeach</div>
@if($learningPath->live_class_enabled)<div class="content-card"><h2>Jadwal live class</h2>@forelse($learningPath->liveSessions->where('starts_at','>=',now()) as $s)<div class="schedule-row"><div><strong>{{ $s->title }}</strong><span>{{ $s->starts_at->format('d M Y, H:i') }} - {{ $s->ends_at->format('H:i') }}</span></div><span>{{ $s->capacity }} kursi</span></div>@empty<p>Jadwal berikutnya akan diumumkan pengajar.</p>@endforelse</div>@endif
<div class="content-card"><h2>Ulasan peserta</h2>@auth @if($isEnrolled)<form method="POST" action="{{ route('reviews.store',$learningPath) }}" class="form-stack review-form">@csrf<label><span>Rating</span><select name="rating" required><option value="5">5 - Sangat baik</option><option value="4">4 - Baik</option><option value="3">3 - Cukup</option><option value="2">2 - Kurang</option><option value="1">1 - Sangat kurang</option></select></label><label><span>Ulasan</span><textarea name="review" rows="3" maxlength="1000" placeholder="Ceritakan pengalaman belajar..."></textarea></label><button class="btn btn-ghost" type="submit">Simpan Ulasan</button></form>@endif @endauth @forelse($learningPath->reviews as $review)<div class="review-row"><strong>{{ $review->user->name }}</strong><span>{{ str_repeat('★',$review->rating) }}</span><p>{{ $review->review }}</p></div>@empty<p>Belum ada ulasan.</p>@endforelse</div>
@auth @if($isEnrolled)<div class="content-card"><h2>Tanya pengajar</h2><form method="POST" action="{{ route('questions.store',$learningPath) }}" class="form-stack">@csrf<textarea name="question" rows="3" placeholder="Tulis pertanyaan tentang course..." required></textarea><button class="btn btn-dark" type="submit">Kirim Pertanyaan</button></form><div class="qa-list">@foreach($learningPath->questions as $q)<article><strong>{{ $q->user->name }}</strong><p>{{ $q->question }}</p>@foreach($q->answers as $a)<div class="answer-box"><small>Jawaban pengajar</small><p>{{ $a->answer }}</p></div>@endforeach</article>@endforeach</div></div>@endif @endauth
</div><aside>
<div class="content-card instructor-card"><h3>Tentang pengajar</h3>@if($learningPath->instructor)<div class="teacher-avatar large">{{ strtoupper(substr($learningPath->instructor->name,0,1)) }}</div><h3>{{ $learningPath->instructor->name }}</h3><p>{{ $learningPath->instructor->instructorProfile?->headline }}</p><div class="path-meta"><span>★ {{ $learningPath->instructor->instructorProfile?->rating ?? 0 }}</span><span>{{ $learningPath->instructor->instructorProfile?->years_experience ?? 0 }} th pengalaman</span></div><a class="card-link" href="{{ route('instructors.show',$learningPath->instructor) }}">Lihat profil →</a>@else<p>Profil pengajar belum tersedia.</p>@endif</div>
<div class="content-card"><h3>Persyaratan</h3><p>{{ $learningPath->requirements ?: 'Perangkat yang dapat mengakses website dan pendampingan orang tua bila diperlukan.' }}</p></div>
</aside></div></section>
@endsection
