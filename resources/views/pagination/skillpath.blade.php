@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="sp-pagination-wrapper">
        <div class="sp-pagination-meta">
            <span>Menampilkan <b>{{ $paginator->firstItem() ?? 0 }}</b> - <b>{{ $paginator->lastItem() ?? 0 }}</b> dari <b>{{ $paginator->total() }}</b> data</span>
        </div>

        <ul class="sp-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="sp-page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                    <span class="sp-page-link" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span class="sp-page-text">Sebelumnya</span>
                    </span>
                </li>
            @else
                <li class="sp-page-item">
                    <a class="sp-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span class="sp-page-text">Sebelumnya</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="sp-page-item disabled dots" aria-disabled="true"><span class="sp-page-link sp-page-dots">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="sp-page-item active" aria-current="page"><span class="sp-page-link">{{ $page }}</span></li>
                        @else
                            <li class="sp-page-item"><a class="sp-page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="sp-page-item">
                    <a class="sp-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">
                        <span class="sp-page-text">Berikutnya</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </li>
            @else
                <li class="sp-page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                    <span class="sp-page-link" aria-hidden="true">
                        <span class="sp-page-text">Berikutnya</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
