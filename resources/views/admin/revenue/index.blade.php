@extends('admin.layouts.app')

@section('title', 'Laporan Pendapatan | Admin SKILLPATH')
@section('page-title', 'Laporan Pendapatan')

@section('content')
<x-admin.feature-header
    eyebrow="Keuangan Platform"
    title="Laporan pendapatan"
    description="Analisis transaksi yang sudah dibayar, tren penjualan, diskon, course, pengajar, dan metode pembayaran."
>
    <x-slot:actions>
        <a class="admin-btn secondary" href="{{ route('admin.revenue.export', request()->query()) }}">Ekspor CSV</a>
    </x-slot:actions>
</x-admin.feature-header>

<section class="admin-section-card compact-section">
    <form class="admin-filter-panel revenue-filter-panel-v2" method="GET" action="{{ route('admin.revenue.index') }}">
        <div class="admin-filter-grid revenue-filter-grid">
            <label class="admin-filter-field">
                <span>Dari tanggal</span>
                <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}">
            </label>

            <label class="admin-filter-field">
                <span>Sampai tanggal</span>
                <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}">
            </label>

            <label class="admin-filter-field">
                <span>Course</span>
                <select name="course_id">
                    <option value="">Semua course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Pengajar</span>
                <select name="instructor_id">
                    <option value="">Semua pengajar</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected((string) request('instructor_id') === (string) $instructor->id)>
                            {{ $instructor->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Metode pembayaran</span>
                <select name="payment_method">
                    <option value="">Semua metode</option>
                    <option value="qris" @selected(request('payment_method') === 'qris')>QRIS</option>
                    <option value="virtual_account" @selected(request('payment_method') === 'virtual_account')>Virtual Account</option>
                    <option value="ewallet" @selected(request('payment_method') === 'ewallet')>E-Wallet</option>
                    <option value="bank_transfer" @selected(request('payment_method') === 'bank_transfer')>Transfer Bank</option>
                </select>
            </label>
        </div>

        <div class="admin-filter-actions">
            <button class="admin-btn primary" type="submit">Terapkan Filter</button>
            <a class="admin-btn ghost" href="{{ route('admin.revenue.index') }}">Reset</a>
        </div>
    </form>
</section>

<div class="admin-metric-grid">
    <x-admin.metric-card
        label="Pendapatan"
        :value="'Rp'.number_format($summary['revenue'], 0, ',', '.')"
        :hint="$from->translatedFormat('d M Y').' – '.$to->translatedFormat('d M Y')"
        tone="green"
    />
    <x-admin.metric-card
        label="Pesanan Dibayar"
        :value="number_format($summary['paid_orders'])"
        :hint="number_format($summary['items_sold']).' course terjual'"
        tone="blue"
    />
    <x-admin.metric-card
        label="Rata-rata Pesanan"
        :value="'Rp'.number_format($summary['average_order'], 0, ',', '.')"
        hint="Nilai rata-rata transaksi terbayar"
        tone="yellow"
    />
    <x-admin.metric-card
        label="Perubahan Pendapatan"
        :value="($summary['revenue_change'] >= 0 ? '+' : '').number_format($summary['revenue_change'], 1).'%'" 
        :hint="'Periode sebelumnya Rp'.number_format($summary['previous_revenue'], 0, ',', '.')"
        :tone="$summary['revenue_change'] >= 0 ? 'green' : 'red'"
    />
</div>

<div class="admin-insight-strip">
    <div>
        <span>Nilai Kotor</span>
        <strong>Rp{{ number_format($summary['gross'], 0, ',', '.') }}</strong>
    </div>
    <div>
        <span>Total Diskon</span>
        <strong>Rp{{ number_format($summary['discount'], 0, ',', '.') }}</strong>
    </div>
    <div>
        <span>Rasio Diskon</span>
        <strong>{{ number_format($summary['discount_rate'], 1) }}%</strong>
    </div>
    <div>
        <span>Periode Pembanding</span>
        <strong>{{ $previousFrom->translatedFormat('d M') }}–{{ $previousTo->translatedFormat('d M Y') }}</strong>
    </div>
</div>

<div class="admin-split-grid revenue-main-grid">
    <section class="admin-section-card">
        <x-admin.section-header
            eyebrow="Tren Pendapatan"
            title="Pergerakan penjualan"
            :description="$trend->count().' periode memiliki transaksi dibayar.'"
        />

        @if($trend->isEmpty())
            <div class="admin-empty-state">
                <strong>Belum ada pendapatan.</strong>
                <span>Tidak ada transaksi PAID pada periode ini.</span>
            </div>
        @else
            <div class="admin-bar-chart" aria-label="Grafik pendapatan">
                @foreach($trend as $point)
                    @php($height = max(5, ($point['revenue'] / $maxTrendRevenue) * 100))
                    <div class="admin-bar-chart-item" title="{{ $point['label'] }}: Rp{{ number_format($point['revenue'], 0, ',', '.') }}">
                        <span class="admin-chart-value">Rp{{ number_format($point['revenue'] / 1000, 0, ',', '.') }}k</span>
                        <div class="admin-chart-column">
                            <span style="height: {{ $height }}%"></span>
                        </div>
                        <small>{{ $point['label'] }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="admin-section-card">
        <x-admin.section-header
            eyebrow="Pembayaran"
            title="Metode pembayaran"
            description="Kontribusi pendapatan berdasarkan metode transaksi."
        />

        <div class="admin-stack-list">
            @forelse($paymentMethods as $method)
                @php($share = $summary['revenue'] > 0 ? ($method->revenue / $summary['revenue']) * 100 : 0)
                <article class="admin-list-card compact-card">
                    <div class="admin-list-content">
                        <div class="admin-list-heading">
                            <div>
                                <strong>{{ strtoupper(str_replace('_', ' ', $method->payment_method ?? 'Tidak diketahui')) }}</strong>
                                <small>{{ $method->orders_count }} pesanan</small>
                            </div>
                            <strong>Rp{{ number_format($method->revenue, 0, ',', '.') }}</strong>
                        </div>
                        <div class="admin-progress-track">
                            <span style="width: {{ $share }}%"></span>
                        </div>
                        <small class="admin-cell-help">{{ number_format($share, 1) }}% dari pendapatan</small>
                    </div>
                </article>
            @empty
                <div class="admin-empty-state">
                    <strong>Belum ada data pembayaran.</strong>
                </div>
            @endforelse
        </div>
    </section>
</div>

<div class="admin-split-grid">
    <section class="admin-section-card">
        <x-admin.section-header eyebrow="Course" title="Pendapatan per course" />

        <div class="admin-ranking-list">
            @forelse($courseRevenue as $row)
                <div class="admin-ranking-item">
                    <span class="admin-rank">{{ $loop->iteration }}</span>
                    <div>
                        <strong>{{ $row->course_title }}</strong>
                        <small>{{ $row->items_sold }} terjual · diskon Rp{{ number_format($row->discount, 0, ',', '.') }}</small>
                    </div>
                    <strong>Rp{{ number_format($row->revenue, 0, ',', '.') }}</strong>
                </div>
            @empty
                <div class="admin-empty-state"><strong>Belum ada penjualan course.</strong></div>
            @endforelse
        </div>
    </section>

    <section class="admin-section-card">
        <x-admin.section-header eyebrow="Pengajar" title="Kontribusi course per pengajar" />

        <div class="admin-ranking-list">
            @forelse($instructorRevenue as $row)
                <div class="admin-ranking-item">
                    <span class="admin-rank">{{ $loop->iteration }}</span>
                    <div>
                        <strong>{{ $row->instructor_name }}</strong>
                        <small>{{ $row->items_sold }} course terjual</small>
                    </div>
                    <strong>Rp{{ number_format($row->revenue, 0, ',', '.') }}</strong>
                </div>
            @empty
                <div class="admin-empty-state"><strong>Belum ada kontribusi pengajar.</strong></div>
            @endforelse
        </div>

        <div class="admin-info-note inside">
            <strong>Catatan</strong>
            <span>Nilai menunjukkan penjualan course terkait pengajar. Belum termasuk komisi, pajak, atau payout.</span>
        </div>
    </section>
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Transaksi"
        title="Detail transaksi terbayar"
        description="Setiap baris merepresentasikan satu course pada pesanan dengan status PAID."
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($transactions->total()) }} item</span>
        </x-slot:actions>
    </x-admin.section-header>

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table revenue-table">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pesanan</th>
                <th>Pembeli</th>
                <th>Course</th>
                <th>Pengajar</th>
                <th>Metode</th>
                <th>Diskon</th>
                <th>Pendapatan</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transactions as $item)
                <tr>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($item->paid_at)->translatedFormat('d M Y') }}</strong>
                        <small class="admin-cell-help">{{ \Carbon\Carbon::parse($item->paid_at)->format('H:i') }}</small>
                    </td>
                    <td><strong>{{ $item->order_number }}</strong></td>
                    <td>{{ $item->buyer_name }}</td>
                    <td>{{ $item->course_title ?? $item->title_snapshot }}</td>
                    <td>{{ $item->instructor_name ?? 'Tidak tersedia' }}</td>
                    <td><span class="admin-chip">{{ strtoupper(str_replace('_', ' ', $item->payment_method ?? '-')) }}</span></td>
                    <td>Rp{{ number_format($item->discount, 0, ',', '.') }}</td>
                    <td><strong>Rp{{ number_format($item->final_price, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="admin-empty-state">
                            <strong>Belum ada transaksi terbayar.</strong>
                            <span>Ubah periode atau filter untuk melihat data lain.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $transactions->links() }}</div>
</section>
@endsection
