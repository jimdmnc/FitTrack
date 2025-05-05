@if (true)  {{-- Always show pagination regardless of page count --}}
    <nav class="flex my-6" aria-label="Pagination">
        <div class="w-full flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Page X of Y info - Top on mobile, Left on desktop -->
            <div class="text-sm text-gray-200 font-semibold order-1 md:order-none">
                Page <span class="font-medium text-[#ff5722]">{{ $paginator->currentPage() }}</span> of {{ $paginator->lastPage() > 0 ? $paginator->lastPage() : 1 }}
            </div>
            
            <!-- Pagination Controls - Centered -->
            <div class="order-3 md:order-none inline-flex md:mx-auto">
                <div class="bg-[#1a1a1a] rounded-full shadow-md px-2 py-1.5 flex items-center justify-center space-x-1">
                    <!-- First Page Button -->
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

                    <!-- Pagination Links (Page Numbers) - Limited to 3 elements on all screen sizes -->
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        
                        // Calculate range for 3 elements
                        $start = max(1, min($currentPage - 1, $lastPage - 2));
                        $end = min($lastPage, $start + 2);
                        
                        // Adjust start if end is maxed out
                        if ($end == $lastPage) {
                            $start = max(1, $lastPage - 2);
                        }
                    @endphp

                    <!-- Pagination with 3 elements for all screen sizes -->
                    <div class="flex">
                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $currentPage)
                                <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 bg-[#ff5722] rounded-full font-medium shadow-md" aria-current="page">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($i) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-transform duration-150 rounded-full bg-[#2c2c2c] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-md">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor
                    </div>

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

                    <!-- Last Page Button -->
                    @if ($paginator->currentPage() < $paginator->lastPage())
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-200 transition-colors duration-150 bg-[#2c2c2c] rounded-full shadow-md hover:translate-y-[-2px] hover:bg-[#ff5722]" aria-label="Last page">
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
            
            <!-- Empty div for spacing on desktop -->
            <div class="hidden md:block"></div>
        </div>
    </nav>
@endif