@extends('layouts.app')
@section('title', 'Pendapatan Pengajar')

@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Keuangan & Finansial</span>
            <h1>Laporan Pendapatan</h1>
            <p>Pantau pendapatan bersih dari seluruh kelas dan siswa yang terdaftar pada kursus Anda.</p>
        </div>
    </div>

    <div class="stat-grid">
        <article>
            <span class="stat-icon tone-green"><x-icon name="earnings" /></span>
            <div>
                <span>Total Pendapatan</span>
                <b>Rp {{ number_format($totalEarnings, 0, ',', '.') }}</b>
                <small>Akumulasi seluruh pembayaran siswa</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-blue"><x-icon name="calendar" /></span>
            <div>
                <span>Bulan Ini</span>
                <b>Rp {{ number_format($thisMonthEarnings, 0, ',', '.') }}</b>
                <small>Pendapatan terverifikasi bulan ini</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-orange"><x-icon name="child" /></span>
            <div>
                <span>Siswa Berbayar</span>
                <b>{{ $paidStudentsCount }}</b>
                <small>Total anak terdaftar lunas</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-pink"><x-icon name="receipt" /></span>
            <div>
                <span>Rata-rata Transaksi</span>
                <b>Rp {{ number_format($avgPerTransaction, 0, ',', '.') }}</b>
                <small>{{ $totalTransactionsCount }} kali transaksi sukses</small>
            </div>
        </article>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Rincian</span>
                    <h2>Pendapatan per Course</h2>
                </div>
            </div>
            @forelse($courseBreakdown as $item)
                <div class="course-admin-row">
                    <div class="row-icon-vector" style="--row-accent:{{ $item['course']->accent }}">
                        <x-icon :name="$item['course']->category->slug" />
                    </div>
                    <div>
                        <h3>{{ $item['course']->title }}</h3>
                        <p>{{ $item['course']->category->name }} • Harga Rp {{ number_format($item['course']->price, 0, ',', '.') }} • {{ $item['transactions_count'] }} transaksi</p>
                    </div>
                    <div style="text-align: right;">
                        <b style="font-family:'Fredoka'; font-size:16px; color:var(--success);">Rp {{ number_format($item['total_income'], 0, ',', '.') }}</b>
                        <small style="display:block; font-size:8px; color:var(--muted);">Total diterima</small>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <x-icon name="earnings" />
                    <div>
                        <b>Belum ada course terdaftar</b>
                        <span>Tambahkan course untuk mulai menerima pendaftaran siswa.</span>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="panel mentor-principle">
            <span class="principle-icon" style="background:#eaf8ef; color:#236b45;"><x-icon name="payment" /></span>
            <h2>Informasi Penyaluran Dana</h2>
            <p class="mentor-note">
                Pendapatan kursus dihitung dari harga kursus siswa dikurangi biaya operasional yang disepakati. Seluruh transaksi pembayaran langsung tercatat secara transparan dan diverifikasi otomatis oleh sistem SkillPath.
            </p>
        </div>
    </div>

    <div class="panel admin-table-panel" style="margin-top: 18px;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Riwayat</span>
                <h2>Daftar Transaksi Siswa</h2>
            </div>
            <span class="helper-badge">{{ $transactions->count() }} transaksi tercatat</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Bayar</th>
                        <th>No. Invoice</th>
                        <th>Siswa & Orang Tua</th>
                        <th>Course & Sesi</th>
                        <th>Metode Pembayaran</th>
                        <th>Nominal Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>
                                <b>{{ $tx->paid_at ? $tx->paid_at->format('d M Y') : $tx->created_at->format('d M Y') }}</b>
                                <small style="display:block; color:var(--muted);">{{ $tx->paid_at ? $tx->paid_at->format('H:i') : '' }} WIB</small>
                            </td>
                            <td>
                                <code style="font-size:10px; background:#f2efff; padding:4px 7px; border-radius:6px; color:var(--purple);">{{ $tx->invoice_code }}</code>
                            </td>
                            <td>
                                <b>{{ optional($tx->enrollment)->child->name ?? '-' }}</b>
                                <small style="display:block; color:var(--muted);">Orang tua: {{ optional($tx->parent)->name ?? '-' }}</small>
                            </td>
                            <td>
                                <b>{{ optional(optional($tx->enrollment)->course)->title ?? '-' }}</b>
                                <small style="display:block; color:var(--muted);">
                                    @if(optional($tx->enrollment)->schedule)
                                        Ruang {{ optional($tx->enrollment)->schedule->room }} ({{ substr(optional($tx->enrollment)->schedule->start_time, 0, 5) }}-{{ substr(optional($tx->enrollment)->schedule->end_time, 0, 5) }})
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $tx->payment_method ?? 'transfer') }}</span>
                            </td>
                            <td>
                                <b style="font-family:'Fredoka'; font-size:13px; color:var(--ink);">Rp {{ number_format($tx->subtotal, 0, ',', '.') }}</b>
                            </td>
                            <td>
                                <span class="status-chip paid">Lunas</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:24px;">
                                <div class="empty-state" style="justify-content:center;">
                                    <x-icon name="receipt" />
                                    <div>
                                        <b>Belum ada riwayat transaksi</b>
                                        <span>Transaksi pembayaran siswa akan otomatis muncul setelah pembayaran diverifikasi.</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
