@if ($paginator->hasPages())
    <nav class="flex my-6" aria-label="Pagination">
        <!-- Main Pagination Container with Flex Layout -->
        <div class="w-full flex items-center justify-between">
            <!-- Page X of Y info - Left Side -->
            <div class="ml-4 text-sm text-gray-200 font-semibold">
                Page <span class="font-medium text-[#ff5722]">{{ $paginator->currentPage() }}</span> of {{ $paginator->lastPage() }}
            </div>
            
            <!-- Pagination Pill - Center -->
            <div class="mx-auto bg-[#2c2c2c] rounded-full shadow-md px-3 py-2 flex items-center">
                <!-- Previous Page Button -->
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-8 h-8 text-gray-200 cursor-not-allowed bg-[#444444] rounded-full shadow-2xl mr-6" aria-disabled="true">
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

                <!-- Page Numbers -->
                @foreach ($elements as $element)
                    <!-- "Three Dots" Separator -->
                    @if (is_string($element))
                        <span class="px-1 text-gray-300 mx-1">{{ $element }}</span>
                    @endif

                    <!-- Array Of Links -->
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 bg-[#ff5722] hover:translate-y-[-2px] rounded-full font-medium shadow-2xl" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 transition-transform duration-150 rounded-full bg-[#444444] hover:bg-[#ff5722] hover:translate-y-[-2px] hover:text-gray-200 shadow-2xl">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

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
            
            <!-- Empty div to balance the layout -->
            <div class="w-24"></div>
        </div>
    </nav>
@endif