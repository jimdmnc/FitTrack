@if (true)  {{-- Always show pagination regardless of page count --}}
    <nav class="flex my-6" aria-label="Pagination">
        <div class="w-full flex items-center justify-between flex-wrap md:flex-nowrap">
            <!-- Page X of Y info - Left Side -->
            <div class="text-sm text-gray-200 font-semibold mb-2 md:mb-0">
                Page <span class="font-medium text-[#ff5722]">{{ $paginator->currentPage() }}</span> of {{ $paginator->lastPage() > 0 ? $paginator->lastPage() : 1 }}
            </div>
            
            <!-- Pagination Pill - Center -->
            <div class="mx-auto bg-[#2c2c2c] rounded-full shadow-md px-3 py-2 flex items-center space-x-3">
                <!-- Previous Page Button -->
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 cursor-not-allowed bg-[#444444] rounded-full shadow-2xl" aria-disabled="true">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="mr-6 inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#444444] rounded-full shadow-2xl hover:translate-y-[-2px] hover:bg-[#ff5722]" rel="prev" aria-label="@lang('pagination.previous')">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                <!-- Pagination Links (Page Numbers) -->
                @php
                    $lastPage = $paginator->lastPage() > 0 ? $paginator->lastPage() : 1;
                    $start = max($paginator->currentPage() - 2, 1);
                    $end = min($start + 4, $lastPage);
                    if ($end - $start < 4) {
                        $start = max($end - 4, 1);
                    }
                @endphp

                <!-- Previous ellipsis if necessary (only on larger screens) -->
                @if ($start > 1 && $lastPage > 5)
                    <span class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 cursor-pointer bg-[#444444] hover:bg-[#ff5722] rounded-full font-medium shadow-2xl hidden md:block">
                        ...
                    </span>
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $paginator->currentPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 bg-[#ff5722] hover:translate-y-[-2px] rounded-full font-medium shadow-2xl" aria-current="page">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 transition-transform duration-150 rounded-full bg-[#444444] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-2xl">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                <!-- Next Page Button -->
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="ml-6 inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#444444] rounded-full shadow-2xl hover:translate-y-[-2px] hover:bg-[#ff5722]" rel="next" aria-label="@lang('pagination.next')">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="ml-6 inline-flex items-center justify-center w-8 h-8 text-gray-300 cursor-not-allowed shadow-2xl bg-[#444444] rounded-full" aria-disabled="true">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
            
            <!-- Show "Next" and "Previous" buttons with ellipses for large pages -->
            @if ($end < $lastPage && $lastPage > 5)
                <span class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 cursor-pointer bg-[#444444] hover:bg-[#ff5722] rounded-full font-medium shadow-2xl hidden md:block">
                    ...
                </span>
            @endif

            <!-- Empty div to balance the layout -->
            <div class="w-24"></div>
        </div>
    </nav>
@endif
