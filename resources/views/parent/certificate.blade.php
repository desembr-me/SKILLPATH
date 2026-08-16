@extends('layouts.app')
@section('title', 'Sertifikat Kelulusan - ' . $certificate->enrollment->child->name)

@push('styles')
<style>
@page {
    size: A4 landscape;
    margin: 0;
}

@media print {
    *, *:before, *:after {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
        box-sizing: border-box !important;
    }
    
    html, body {
        width: 297mm !important;
        height: 210mm !important;
        max-width: 297mm !important;
        max-height: 210mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: hidden !important;
    }

    main {
        width: 297mm !important;
        height: 210mm !important;
        max-width: 297mm !important;
        max-height: 210mm !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        display: block !important;
    }

    .no-print,
    .site-header,
    .site-footer,
    .site-footer-compact,
    .mobile-header-actions,
    .mobile-nav-backdrop,
    .mobile-nav-drawer,
    .mobile-bottom-nav,
    .dash-title,
    .dash-actions,
    .invoice-bottom-actions,
    .flash,
    .btn,
    .payment-toast,
    .admin-sidebar,
    .admin-header,
    .admin-mobile-header,
    .admin-bottom-nav {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .certificate-page {
        width: 297mm !important;
        height: 210mm !important;
        max-width: 297mm !important;
        max-height: 210mm !important;
        margin: 0 !important;
        padding: 6mm 8mm !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #ffffff !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .certificate-sheet,
    .certificate-wrapper {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    .certificate-border {
        width: 281mm !important;
        height: 198mm !important;
        max-width: 281mm !important;
        max-height: 198mm !important;
        border: 8px solid #201c4b !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        padding: 16px 36px 14px !important;
        background: #ffffff !important;
        background: radial-gradient(circle at center, #ffffff 0%, #fffdf8 70%, #fbf8ef 100%) !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        align-items: center !important;
        text-align: center !important;
        position: relative !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
        page-break-before: avoid !important;
        break-before: avoid !important;
    }

    .certificate-border:before {
        inset: 5px !important;
        border: 1.5px solid #d4af37 !important;
        border-radius: 2px !important;
    }

    .certificate-inner-frame {
        inset: 9px !important;
        border: 1px dashed rgba(212, 175, 55, 0.4) !important;
    }

    .cert-corner {
        width: 32px !important;
        height: 32px !important;
    }
    .cert-corner-tl { top: 6px !important; left: 6px !important; }
    .cert-corner-tr { top: 6px !important; right: 6px !important; }
    .cert-corner-bl { bottom: 6px !important; left: 6px !important; }
    .cert-corner-br { bottom: 6px !important; right: 6px !important; }

    /* Header */
    .cert-header {
        margin: 0 !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .cert-brand-bar {
        margin-bottom: 4px !important;
    }
    .cert-brand-logo {
        height: 26px !important;
    }
    .cert-ribbon-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        background: #d4af37 !important;
        background: linear-gradient(135deg, #d4af37 0%, #f59e0b 50%, #b45309 100%) !important;
        color: #ffffff !important;
        padding: 2px 14px !important;
        border-radius: 999px !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
        letter-spacing: 1.5px !important;
        text-transform: uppercase !important;
        border: 1px solid #b45309 !important;
        margin-bottom: 4px !important;
    }
    .cert-main-title {
        font-family: "Fredoka", "Nunito", sans-serif !important;
        font-size: 22px !important;
        font-weight: 700 !important;
        letter-spacing: 2px !important;
        color: #1e1b4b !important;
        text-transform: uppercase !important;
        margin: 0 !important;
        line-height: 1.15 !important;
    }
    .cert-sub-title {
        font-size: 8px !important;
        font-weight: 700 !important;
        letter-spacing: 2px !important;
        color: #d97706 !important;
        text-transform: uppercase !important;
        margin-top: 1px !important;
    }
    .cert-header-divider {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        width: 100% !important;
        max-width: 220px !important;
        margin: 3px auto 0 !important;
    }
    .cert-header-divider .divider-line {
        flex: 1 !important;
        height: 1px !important;
        background: #d4af37 !important;
    }
    .cert-header-divider .divider-star {
        color: #d4af37 !important;
        font-size: 9px !important;
    }

    /* Body */
    .cert-body {
        margin: 2px 0 !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }
    .cert-presentation-text {
        font-size: 9px !important;
        font-weight: 600 !important;
        letter-spacing: 1px !important;
        color: #64748b !important;
        text-transform: uppercase !important;
        margin-bottom: 2px !important;
    }
    .cert-child-name-box {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        margin: 1px 0 3px !important;
    }
    .cert-child-name-box .star-accent {
        color: #f59e0b !important;
        font-size: 16px !important;
    }
    .cert-child-name {
        font-family: "Fredoka", "Nunito", sans-serif !important;
        font-size: 28px !important;
        font-weight: 700 !important;
        color: #1e1b4b !important;
        -webkit-text-fill-color: #1e1b4b !important;
        background: none !important;
        padding: 0 14px 2px !important;
        border-bottom: 2px solid #d4af37 !important;
        margin: 0 !important;
        line-height: 1.1 !important;
    }
    .cert-description {
        max-width: 560px !important;
        font-size: 10px !important;
        line-height: 1.4 !important;
        color: #334155 !important;
        margin: 0 auto 4px !important;
    }
    .cert-course-highlight {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        background: #f1efff !important;
        border: 1px solid #c7d2fe !important;
        border-radius: 999px !important;
        padding: 2.5px 12px !important;
        margin-bottom: 3px !important;
    }
    .cert-course-highlight strong {
        font-family: "Fredoka", sans-serif !important;
        font-size: 12px !important;
        color: #1e1b4b !important;
    }
    .cert-course-highlight .category-chip {
        background: #6857df !important;
        color: #ffffff !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
        padding: 1.5px 6px !important;
        border-radius: 999px !important;
        text-transform: uppercase !important;
    }
    .cert-merit-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        background: #fffbeb !important;
        border: 1px solid #fde68a !important;
        border-radius: 999px !important;
        padding: 2px 10px !important;
        font-size: 8.5px !important;
        color: #92400e !important;
        font-weight: 600 !important;
    }

    /* Footer */
    .cert-footer {
        width: 100% !important;
        gap: 3px !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }
    .cert-signatures-grid {
        display: grid !important;
        grid-template-columns: 1fr 80px 1fr !important;
        align-items: end !important;
        gap: 10px !important;
        width: 100% !important;
        max-width: 680px !important;
        margin: 0 auto !important;
    }
    .cert-sign-col {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
    }
    .cert-sign-svg {
        height: 24px !important;
        width: 95px !important;
        margin-bottom: 2px !important;
        color: #1e1b4b !important;
    }
    .cert-sign-line {
        width: 110px !important;
        height: 1.5px !important;
        background: #201c4b !important;
        margin-bottom: 2px !important;
    }
    .cert-sign-name {
        font-family: "Fredoka", sans-serif !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #1e1b4b !important;
    }
    .cert-sign-role {
        font-size: 7px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        text-transform: uppercase !important;
        letter-spacing: 0.4px !important;
        margin-top: 1px !important;
    }
    .cert-seal-col {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .cert-rosette-seal {
        width: 50px !important;
        height: 50px !important;
    }
    .cert-meta-bottom {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        width: 100% !important;
        border-top: 1px solid rgba(212, 175, 55, 0.35) !important;
        padding-top: 3px !important;
        font-size: 7.5px !important;
        color: #64748b !important;
    }
    .cert-serial-tag {
        font-weight: 500 !important;
        color: #475569 !important;
    }
    .cert-auth-tag {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        color: #15803d !important;
        font-weight: 600 !important;
    }
}
</style>
@endpush

@section('content')
@php
    $child = $certificate->enrollment->child;
    $course = $certificate->enrollment->course;
    $category = $course->category ?? null;
    $instructor = $course->instructor ?? null;
    $examAttempt = $certificate->examAttempt;
    $exam = $examAttempt->exam ?? null;
    $score = $examAttempt->score ?? 100;
    $passingScore = $exam->passing_score ?? 75;

    $predicate = match(true) {
        $score >= 90 => 'Istimewa (High Distinction)',
        $score >= 80 => 'Sangat Baik (Distinction)',
        $score >= 75 => 'Baik Sekali (Merit)',
        default => 'Lulus Memuaskan (Pass)',
    };
    $certNo = $certificate->certificate_no ?? $certificate->certificate_number ?? ('CERT-SP-' . $certificate->id);
    $issueDate = $certificate->issued_at ? $certificate->issued_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
@endphp

<section class="dashboard-page certificate-page">
    {{-- Header Action Toolbar (Hidden during print) --}}
    <div class="dash-title no-print">
        <div>
            <span class="eyebrow">Prestasi & Kelulusan Siswa</span>
            <h1>Sertifikat Kelulusan Resmi</h1>
            <p>Diterbitkan untuk <b>{{ $child->name }}</b> atas keberhasilan menyelesaikan program <b>{{ $course->title }}</b>.</p>
        </div>
        <div class="dash-actions">
            <button class="btn btn-primary" onclick="window.print()">
                <x-icon name="certificate" /> Cetak / Simpan PDF (A4)
            </button>
            <button class="btn btn-ghost" onclick="copyCertLink()">
                <x-icon name="copy" /> Salin Link
            </button>
        </div>
    </div>

    {{-- Interactive Toast Message --}}
    <div id="certToast" class="payment-toast" style="display:none;">Link sertifikat berhasil disalin!</div>

    {{-- A4 Landscape Certificate Sheet --}}
    <div class="certificate-sheet">
        <div class="certificate-wrapper">
            <div class="certificate-border">
                {{-- Decorative Guilloché Watermark & Inner Frame --}}
                <div class="certificate-watermark"></div>
                <div class="certificate-inner-frame"></div>

                {{-- Golden Baroque Corner Ornaments --}}
                <svg class="cert-corner cert-corner-tl" viewBox="0 0 44 44" fill="none">
                    <path d="M4 44V12C4 7.58172 7.58172 4 12 4H44" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
                    <path d="M10 44V18C10 13.5817 13.5817 10 18 10H44" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 3"/>
                    <circle cx="12" cy="12" r="3.5" fill="#d4af37"/>
                    <polygon points="12,5 14,10 19,12 14,14 12,19 10,14 5,12 10,10" fill="#f59e0b"/>
                </svg>
                <svg class="cert-corner cert-corner-tr" viewBox="0 0 44 44" fill="none">
                    <path d="M4 44V12C4 7.58172 7.58172 4 12 4H44" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
                    <path d="M10 44V18C10 13.5817 13.5817 10 18 10H44" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 3"/>
                    <circle cx="12" cy="12" r="3.5" fill="#d4af37"/>
                    <polygon points="12,5 14,10 19,12 14,14 12,19 10,14 5,12 10,10" fill="#f59e0b"/>
                </svg>
                <svg class="cert-corner cert-corner-bl" viewBox="0 0 44 44" fill="none">
                    <path d="M4 44V12C4 7.58172 7.58172 4 12 4H44" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
                    <path d="M10 44V18C10 13.5817 13.5817 10 18 10H44" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 3"/>
                    <circle cx="12" cy="12" r="3.5" fill="#d4af37"/>
                    <polygon points="12,5 14,10 19,12 14,14 12,19 10,14 5,12 10,10" fill="#f59e0b"/>
                </svg>
                <svg class="cert-corner cert-corner-br" viewBox="0 0 44 44" fill="none">
                    <path d="M4 44V12C4 7.58172 7.58172 4 12 4H44" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
                    <path d="M10 44V18C10 13.5817 13.5817 10 18 10H44" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 3"/>
                    <circle cx="12" cy="12" r="3.5" fill="#d4af37"/>
                    <polygon points="12,5 14,10 19,12 14,14 12,19 10,14 5,12 10,10" fill="#f59e0b"/>
                </svg>

                {{-- 1. Certificate Top Branding & Title Header --}}
                <div class="cert-header">
                    <div class="cert-brand-bar">
                        <img class="cert-brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath Academy">
                    </div>
                    <div class="cert-ribbon-badge">
                        <span>★</span>
                        <span>Official Certificate of Achievement</span>
                        <span>★</span>
                    </div>
                    <h1 class="cert-main-title">Sertifikat Kelulusan</h1>
                    <span class="cert-sub-title">SkillPath Academy Indonesia</span>
                    <div class="cert-header-divider">
                        <span class="divider-line"></span>
                        <span class="divider-star">✦ ✦ ✦</span>
                        <span class="divider-line"></span>
                    </div>
                </div>

                {{-- 2. Recipient Section (Child Name & Description) --}}
                <div class="cert-body">
                    <span class="cert-presentation-text">Dengan bangga dan apresiasi setinggi-tingginya diberikan kepada:</span>
                    <div class="cert-child-name-box">
                        <span class="star-accent">⭐</span>
                        <h2 class="cert-child-name">{{ $child->name }}</h2>
                        <span class="star-accent">⭐</span>
                    </div>

                    <p class="cert-description">
                        Atas komitmen belajar, penyelesaian seluruh modul materi dan karya praktik mandiri, serta dinyatakan <b>LULUS</b> ujian akhir dengan hasil membanggakan pada kursus:
                    </p>

                    <div class="cert-course-highlight">
                        <strong>{{ $course->title }}</strong>
                        @if($category)
                            <span class="category-chip">{{ $category->name }}</span>
                        @endif
                    </div>

                    <div>
                        <span class="cert-merit-badge">
                            <span>🏆</span>
                            <span>Nilai Akhir: <b>{{ $score }}</b> / 100</span>
                            <span>•</span>
                            <span>Predikat: <b>{{ $predicate }}</b></span>
                        </span>
                    </div>
                </div>

                {{-- 3. Signatures, Golden Rosette Seal, & Date Footer --}}
                <div class="cert-footer">
                    <div class="cert-signatures-grid">
                        {{-- Left Column: Instructor / Mentor --}}
                        <div class="cert-sign-col">
                            <svg class="cert-sign-svg" viewBox="0 0 160 45" fill="none" stroke="#201c4b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 32 C25 15, 30 8, 42 12 C55 18, 48 38, 38 35 C30 32, 50 15, 65 24 C75 30, 85 20, 95 26 C105 32, 115 18, 130 22 C140 25, 148 20, 152 28"/>
                                <path d="M25 24 L145 28" stroke="#201c4b" stroke-width="1.2" stroke-dasharray="3 2" opacity="0.3"/>
                            </svg>
                            <div class="cert-sign-line"></div>
                            <span class="cert-sign-name">{{ $instructor->name ?? 'Mentor Kursus' }}</span>
                            <span class="cert-sign-role">Mentor & Pengajar Kursus</span>
                        </div>

                        {{-- Center Column: 3D Golden Rosette Medal --}}
                        <div class="cert-seal-col">
                            <div class="cert-rosette-seal">
                                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#fff099"/>
                                            <stop offset="30%" stop-color="#ffd700"/>
                                            <stop offset="70%" stop-color="#d4af37"/>
                                            <stop offset="100%" stop-color="#996515"/>
                                        </linearGradient>
                                        <linearGradient id="ribbonGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#f43f5e"/>
                                            <stop offset="100%" stop-color="#9f1239"/>
                                        </linearGradient>
                                        <filter id="medalShadow" x="-10%" y="-10%" width="130%" height="130%">
                                            <feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.35"/>
                                        </filter>
                                    </defs>
                                    <!-- Ribbon Tails -->
                                    <path d="M34 62L22 94L38 86L48 94L42 62Z" fill="url(#ribbonGrad)"/>
                                    <path d="M66 62L58 94L68 86L84 94L72 62Z" fill="url(#ribbonGrad)"/>
                                    <!-- Rosette Outer Starburst -->
                                    <path d="M50 6L55 16L66 12L67 23L78 24L75 35L85 40L78 48L85 56L75 61L78 72L67 73L66 84L55 80L50 90L45 80L34 84L33 73L22 72L25 61L15 56L22 48L15 40L25 35L22 24L33 23L34 12L45 16Z" fill="url(#goldGrad)" filter="url(#medalShadow)"/>
                                    <!-- Inner Rosette Ring -->
                                    <circle cx="50" cy="48" r="28" fill="#201c4b" stroke="#ffd700" stroke-width="2"/>
                                    <circle cx="50" cy="48" r="23" fill="none" stroke="#ffd700" stroke-width="0.8" stroke-dasharray="2 2"/>
                                    <!-- Star & Badge Icon -->
                                    <polygon points="50,30 53,39 62,39 55,44 58,53 50,48 42,53 45,44 38,39 47,39" fill="url(#goldGrad)"/>
                                    <text x="50" y="60" font-size="4.2" font-family="Arial, sans-serif" font-weight="900" fill="#ffd700" text-anchor="middle" letter-spacing="0.8">EXCELLENCE</text>
                                    <text x="50" y="66" font-size="3" font-family="Arial, sans-serif" font-weight="bold" fill="#ffffff" text-anchor="middle" letter-spacing="0.4">SKILLPATH</text>
                                </svg>
                            </div>
                        </div>

                        {{-- Right Column: Academic Director & Date --}}
                        <div class="cert-sign-col">
                            <svg class="cert-sign-svg" viewBox="0 0 160 45" fill="none" stroke="#201c4b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 28 C20 10, 28 5, 35 15 C42 28, 36 38, 48 25 C60 12, 70 32, 82 22 C92 12, 105 30, 118 20 C130 10, 142 22, 150 18"/>
                                <path d="M40 36 C70 34, 110 32, 145 35" stroke="#201c4b" stroke-width="1.5"/>
                            </svg>
                            <div class="cert-sign-line"></div>
                            <span class="cert-sign-name">Dr. Hendra Wijaya, M.Ed.</span>
                            <span class="cert-sign-role">Direktur Akademik SkillPath</span>
                        </div>
                    </div>

                    {{-- Bottom Security & Verification Bar --}}
                    <div class="cert-meta-bottom">
                        <span class="cert-serial-tag">No. Seri: <b>{{ $certNo }}</b></span>
                        <span class="cert-auth-tag">
                            <x-icon name="check" style="width:12px; height:12px;" />
                            <span>Terverifikasi Resmi & Sah • Diterbitkan pada {{ $issueDate }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Interactive Script --}}
<script>
function copyCertLink() {
    const url = window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link sertifikat berhasil disalin ke clipboard!');
        }).catch(() => {
            promptCopy(url);
        });
    } else {
        promptCopy(url);
    }
}

function promptCopy(text) {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    try {
        document.execCommand('copy');
        showToast('Link sertifikat berhasil disalin!');
    } catch(e) {
        showToast('Gagal menyalin otomatis. Silakan salin URL browser.');
    }
    document.body.removeChild(el);
}

function showToast(msg) {
    let t = document.getElementById('certToast');
    if (!t) return;
    t.textContent = msg;
    t.style.display = 'block';
    t.classList.add('show');
    setTimeout(() => {
        t.classList.remove('show');
        t.style.display = 'none';
    }, 3000);
}
</script>
@endsection
