@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="mt-10">
        <div class="flex items-center justify-center gap-3">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex h-10 w-10 items-center shadow-md justify-center rounded-full bg-white text-slate-300 ring-1 ring-slate-200"
                    aria-disabled="true"
                    aria-label="Previous"
                >
                    <i class="bi bi-arrow-left"></i>
                </span>
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-800 transition"
                    rel="prev"
                    aria-label="Previous"
                >
                    <i class="bi bi-arrow-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="inline-flex h-10 px-2 items-center justify-center text-slate-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#51A3EE] text-white shadow-md"
                                aria-current="page"
                            >
                                {{ $page }}
                            </span>
                        @else
                            <a
                                href="{{ $url }}"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-md text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-900 transition"
                                aria-label="Go to page {{ $page }}"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    class="inline-flex h-10 w-10 items-center justify-center shadow-md rounded-full bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-800 transition"
                    rel="next"
                    aria-label="Next"
                >
                    <i class="bi bi-arrow-right"></i>
                </a>
            @else
                <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-300 ring-1 ring-slate-200"
                    aria-disabled="true"
                    aria-label="Next"
                >
                    <i class="bi bi-arrow-right"></i>
                </span>
            @endif

        </div>
    </nav>
@endif
