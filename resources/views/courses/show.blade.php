@extends('layouts.app')
@section('title',$learningPath->title.' | SKILLPATH')
@section('content')
<section class="course-detail-hero"><div class="container course-detail-grid">
<div>
<div class="course-category-line">@foreach($learningPath->categories as $c)<a href="{{ route('explore.index',['category'=>$c->slug]) }}">{{ $c->name }}</a>@endforeach</div>
<h1>{{ $learningPath->title }}</h1><p class="course-lead">{{ $learningPath->description }}</p>
<div class="rating-line big"><strong>{{ number_format($averageRating,1) }}</strong><span>★</span><small>Mentor</small><small>Platform {{ number_format($platformRating,1) }} ★</small><small>{{ $reviewCount }} ulasan</small><small>{{ $studentCount }} peserta</small><small>Usia {{ $learningPath->min_age }}–{{ $learningPath->max_age }}</small><small>{{ $learningPath->level }}</small><small>Offline</small></div>
@if($learningPath->instructor)<a class="teacher-mini" href="{{ route('instructors.show',$learningPath->instructor) }}"><span class="teacher-avatar photo-avatar small-photo">@if($learningPath->instructor->instructorProfile?->photoSrc())<img src="{{ $learningPath->instructor->instructorProfile->photoSrc() }}" alt="Foto {{ $learningPath->instructor->name }}">@else<span>{{ strtoupper(substr($learningPath->instructor->name,0,1)) }}</span>@endif</span><span><small>Pengajar</small><strong>{{ $learningPath->instructor->name }}</strong></span></a>@endif
</div>
<aside class="purchase-card">
<div class="course-thumb large">@if($learningPath->thumbnailSrc())<img src="{{ $learningPath->thumbnailSrc() }}" alt="Gambar {{ $learningPath->title }}">@else<span>{{ $learningPath->icon }}</span>@endif<small>KELAS TATAP MUKA</small></div>
@if($learningPath->is_free)<div class="purchase-price"><strong>Gratis</strong></div>@else<div class="purchase-price">@if($learningPath->sale_price)<del>Rp{{ number_format($learningPath->price,0,',','.') }}</del><span class="discount-badge">-{{ $learningPath->discountPercent() }}%</span>@endif<strong>Rp{{ number_format($learningPath->effectivePrice(),0,',','.') }}</strong></div>@endif
@auth
@if(auth()->user()->role==='parent')
@if($isEnrolled)<a class="btn btn-dark btn-full" href="{{ route('learning.path',$learningPath) }}">Lihat Kelas Saya</a>
@elseif($learningPath->is_free)<form method="POST" action="{{ route('courses.enroll-free',$learningPath) }}">@csrf<button class="btn btn-blue btn-full" type="submit">Daftar Kelas Gratis</button></form>
@else<form method="POST" action="{{ route('cart.add',$learningPath) }}">@csrf<button class="btn btn-dark btn-full" type="submit">Tambah ke Keranjang</button></form>@endif
<form method="POST" action="{{ route('wishlist.toggle',$learningPath) }}" class="mt-10">@csrf<button class="btn btn-ghost btn-full" type="submit">{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}</button></form>
@endif
@else<a class="btn btn-dark btn-full" href="{{ route('register') }}">Daftar untuk Mengikuti</a>@endauth
<ul class="purchase-features"><li>✓ Level {{ $learningPath->level }}</li><li>✓ {{ $learningPath->modules->count() }} rangkaian aktivitas</li><li>✓ Durasi program ±{{ $learningPath->duration_minutes }} menit</li><li>✓ Informasi kelas & progres tersimpan di akun</li><li>✓ {{ $learningPath->certificate_enabled ? 'Sertifikat setelah lulus ujian akhir' : 'Tanpa sertifikat' }}</li><li>✓ {{ $learningPath->live_class_enabled ? 'Jadwal kelas tatap muka tersedia' : 'Jadwal kelas diumumkan pengajar' }}</li></ul>
</aside></div></section>
<section class="section"><div class="container course-content-grid"><div>
<div class="content-card"><h2>Yang akan dipelajari</h2><p>{{ $learningPath->learning_outcomes ?: 'Anak akan menyelesaikan aktivitas bertahap yang dirancang untuk mengembangkan skill melalui praktik.' }}</p></div>
<div class="content-card"><h2>Rangkaian kegiatan kelas</h2>@foreach($learningPath->modules as $module)<details class="curriculum-item" @if($loop->first) open @endif><summary><strong>{{ $loop->iteration }}. {{ $module->title }}</strong><span>{{ $module->activities->count() }} aktivitas</span></summary><p>{{ $module->summary }}</p><ul>@foreach($module->activities as $a)<li>{{ $a->title }} <small>{{ ['project'=>'PRAKTIK','quiz'=>'LATIHAN','reflection'=>'REFLEKSI','video'=>'MATERI'][$a->type] ?? strtoupper($a->type) }}</small></li>@endforeach</ul></details>@endforeach</div>
@if($learningPath->live_class_enabled)<div class="content-card"><h2>Jadwal kelas tatap muka</h2>@forelse($learningPath->liveSessions->where('starts_at','>=',now()) as $s)<div class="schedule-row"><div><strong>{{ $s->title }}</strong><span>{{ $s->starts_at->format('d M Y, H:i') }} - {{ $s->ends_at->format('H:i') }}@if($s->location) · {{ $s->location }}@endif</span></div><span>{{ $s->capacity }} kursi</span></div>@empty<p>Jadwal berikutnya akan diumumkan pengajar.</p>@endforelse</div>@endif
<div class="content-card">
<h2>Ulasan peserta</h2>
@auth
@if($canReview)
<form method="POST" action="{{ route('reviews.store',$learningPath) }}" class="form-stack review-form">
@csrf
@if($myReview && !$myReview->is_approved)<p><small>Ulasan terakhir sedang menunggu moderasi Admin. Anda tetap dapat memperbaruinya.</small></p>@endif
<div class="two-fields">
<label><span>Rating mentor</span><select name="mentor_rating" required>@for($rating=5;$rating>=1;$rating--)<option value="{{ $rating }}" @selected((int)old('mentor_rating',$myReview?->mentor_rating ?? $myReview?->rating ?? 5)===$rating)>{{ $rating }} - {{ [5=>'Sangat baik',4=>'Baik',3=>'Cukup',2=>'Kurang',1=>'Sangat kurang'][$rating] }}</option>@endfor</select></label>
<label><span>Rating platform SkillPath</span><select name="platform_rating" required>@for($rating=5;$rating>=1;$rating--)<option value="{{ $rating }}" @selected((int)old('platform_rating',$myReview?->platform_rating ?? $myReview?->rating ?? 5)===$rating)>{{ $rating }} - {{ [5=>'Sangat baik',4=>'Baik',3=>'Cukup',2=>'Kurang',1=>'Sangat kurang'][$rating] }}</option>@endfor</select></label>
</div>
<label><span>Ulasan mentor</span><textarea name="mentor_review" rows="3" maxlength="1000" placeholder="Ceritakan pengalaman bersama mentor...">{{ old('mentor_review',$myReview?->mentor_review ?? $myReview?->review) }}</textarea></label>
<label><span>Ulasan platform</span><textarea name="platform_review" rows="3" maxlength="1000" placeholder="Ceritakan pengalaman menggunakan SkillPath...">{{ old('platform_review',$myReview?->platform_review) }}</textarea></label>
<button class="btn btn-ghost" type="submit">{{ $myReview ? 'Perbarui Ulasan' : 'Simpan Ulasan' }}</button>
</form>
@endif
@endauth
@forelse($learningPath->reviews as $review)
<div class="review-row">
<strong>{{ $review->user->name }}</strong>
<p><span>{{ str_repeat('★',$review->mentor_rating ?? $review->rating) }}</span> Mentor @if($review->mentor_review)<br>{{ $review->mentor_review }}@endif</p>
<p><span>{{ str_repeat('★',$review->platform_rating ?? $review->rating) }}</span> Platform @if($review->platform_review)<br>{{ $review->platform_review }}@endif</p>
</div>
@empty<p>Belum ada ulasan.</p>@endforelse
</div>
@auth @if($isEnrolled)<div class="content-card"><h2>Tanya pengajar</h2><form method="POST" action="{{ route('questions.store',$learningPath) }}" class="form-stack">@csrf<textarea name="question" rows="3" placeholder="Tulis pertanyaan tentang kelas, jadwal, atau perlengkapan..." required></textarea><button class="btn btn-dark" type="submit">Kirim Pertanyaan</button></form><div class="qa-list">@foreach($learningPath->questions as $q)<article><strong>{{ $q->user->name }}</strong><p>{{ $q->question }}</p>@foreach($q->answers as $a)<div class="answer-box"><small>Jawaban pengajar</small><p>{{ $a->answer }}</p></div>@endforeach</article>@endforeach</div></div>@endif @endauth
</div><aside>
<div class="content-card instructor-card"><h3>Tentang pengajar</h3>@if($learningPath->instructor)<div class="teacher-avatar large photo-avatar">@if($learningPath->instructor->instructorProfile?->photoSrc())<img src="{{ $learningPath->instructor->instructorProfile->photoSrc() }}" alt="Foto {{ $learningPath->instructor->name }}">@else<span>{{ strtoupper(substr($learningPath->instructor->name,0,1)) }}</span>@endif</div><h3>{{ $learningPath->instructor->name }}</h3><p>{{ $learningPath->instructor->instructorProfile?->headline }}</p><div class="path-meta"><span>★ {{ $learningPath->instructor->instructorProfile?->rating ?? 0 }}</span><span>{{ $learningPath->instructor->instructorProfile?->years_experience ?? 0 }} th pengalaman</span></div><a class="card-link" href="{{ route('instructors.show',$learningPath->instructor) }}">Lihat profil →</a>@else<p>Profil pengajar belum tersedia.</p>@endif</div>
<div class="content-card"><h3>Persyaratan</h3><p>{{ $learningPath->requirements ?: 'Perlengkapan kelas sesuai arahan pengajar dan pendampingan orang tua bila diperlukan.' }}</p></div>
</aside></div></section>
@endsection
