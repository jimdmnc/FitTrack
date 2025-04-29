@if (true)  {{-- Always show pagination regardless of page count --}}
    <nav class="flex my-6" aria-label="Pagination">
        <div class="w-full flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Page X of Y info - Top on mobile, Left on desktop -->
            <div class="text-sm text-gray-200 font-semibold order-1 md:order-none">
                Page <span class="font-medium text-[#ff5722]">{{ $paginator->currentPage() }}</span> of {{ $paginator->lastPage() > 0 ? $paginator->lastPage() : 1 }}
            </div>
            
            <!-- Pagination Controls - Just enough width to contain elements -->
            <div class="order-3 md:order-none inline-flex">
                <div class="bg-[#1a1a1a] rounded-full shadow-md px-2 py-1.5 flex items-center justify-center space-x-1">
                    <!-- First Page Button - Hidden on smallest screens -->
                    <div class="hidden xs:block">
                        @if ($paginator->currentPage() > 1)
                            <a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#2c2c2c] rounded-full shadow-md hover:translate-y-[-2px] hover:bg-[#ff5722]" aria-label="First page">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 cursor-not-allowed bg-[#2c2c2c] rounded-full shadow-md" aria-disabled="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    <!-- Previous Page Button -->
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 cursor-not-allowed bg-[#2c2c2c] rounded-full shadow-md" aria-disabled="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#2c2c2c] rounded-full shadow-md hover:translate-y-[-2px] hover:bg-[#ff5722]" rel="prev" aria-label="@lang('pagination.previous')">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    <!-- Pagination Links (Page Numbers) -->
                    @php
                        $lastPage = $paginator->lastPage() > 0 ? $paginator->lastPage() : 1;
                        $currentPage = $paginator->currentPage();
                        $showAll = $lastPage <= 5;
                        
                        if ($showAll) {
                            $start = 1;
                            $end = $lastPage;
                        } else {
                            $start = max($currentPage - 1, 1);
                            $end = min($currentPage + 1, $lastPage);
                            
                            // Adjust if we're at the beginning or end
                            if ($currentPage <= 2) {
                                $end = 3;
                            } elseif ($currentPage >= $lastPage - 1) {
                                $start = $lastPage - 2;
                            }
                        }
                    @endphp

                    <!-- Always show first page if not in current range -->
                    @if (!$showAll && $start > 1)
                        <a href="{{ $paginator->url(1) }}" class="hidden sm:inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-transform duration-150 rounded-full bg-[#2c2c2c] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-md">
                            1
                        </a>
                        @if ($start > 2)
                            <span class="hidden sm:inline-flex items-center justify-center text-gray-400">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 bg-[#ff5722] rounded-full font-medium shadow-md" aria-current="page">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $paginator->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-transform duration-150 rounded-full bg-[#2c2c2c] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-md">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    <!-- Always show last page if not in current range -->
                    @if (!$showAll && $end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="hidden sm:inline-flex items-center justify-center text-gray-400">...</span>
                        @endif
                        <a href="{{ $paginator->url($lastPage) }}" class="hidden sm:inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-transform duration-150 rounded-full bg-[#2c2c2c] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-md">
                            {{ $lastPage }}
                        </a>
                    @endif

                    <!-- Next Page Button -->
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#2c2c2c] rounded-full shadow-md hover:translate-y-[-2px] hover:bg-[#ff5722]" rel="next" aria-label="@lang('pagination.next')">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 cursor-not-allowed shadow-md bg-[#2c2c2c] rounded-full" aria-disabled="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                    <!-- Last Page Button - Hidden on smallest screens -->
                    <div class="hidden xs:block">
                        @if ($paginator->currentPage() < $lastPage)
                            <a href="{{ $paginator->url($lastPage) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#2c2c2c] rounded-full shadow-md hover:translate-y-[-2px] hover:bg-[#ff5722]" aria-label="Last page">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 cursor-not-allowed bg-[#2c2c2c] rounded-full shadow-md" aria-disabled="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Empty div to balance the layout - Hidden on mobile -->
            <div class="hidden md:block w-24 order-2 md:order-none"></div>
        </div>
    </nav>
@endif