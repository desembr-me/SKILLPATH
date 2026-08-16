@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="sp-pagination-wrapper sp-simple-pagination">
        <ul class="sp-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="sp-page-item disabled" aria-disabled="true">
                    <span class="sp-page-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span>Sebelumnya</span>
                    </span>
                </li>
            @else
                <li class="sp-page-item">
                    <a class="sp-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span>Sebelumnya</span>
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="sp-page-item">
                    <a class="sp-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <span>Berikutnya</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </li>
            @else
                <li class="sp-page-item disabled" aria-disabled="true">
                    <span class="sp-page-link">
                        <span>Berikutnya</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
