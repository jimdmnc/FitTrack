@if ($paginator->hasPages())
    <nav class="flex flex-col my-6" aria-label="Pagination">
        <div class="flex items-center justify-between w-full">
            <!-- Current Page Info - Left Aligned -->
            <div class="text-sm text-gray-300">
                Page <span class="font-medium text-[#ff5722]">{{ $paginator->currentPage() }}</span> of {{ $paginator->lastPage() }}
            </div>

            <!-- Centered Pagination Controls -->
            <div class="flex items-center rounded-lg shadow-sm mx-auto">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-10 h-10 text-gray-200 cursor-not-allowed" aria-disabled="true">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 text-gray-200 transition-colors duration-150 hover:text-[#ff5722]" rel="prev" aria-label="@lang('pagination.previous')">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                <div class="flex px-2 items-center"> <!-- Changed from hidden to flex -->
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-1 text-gray-200">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="inline-flex items-center justify-center w-8 h-8 mx-1 text-white bg-[#ff5722] rounded-full" aria-current="page">
                                        {{ $page }}
                                    </span>
                                @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 mx-1 text-gray-200 transition-transform duration-150 rounded-full hover:bg-[#ff5722] hover:scale-110">
                                    {{ $page }}
                                </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-10 h-10 text-gray-200 transition-colors duration-150 hover:text-[#ff5722]" rel="next" aria-label="@lang('pagination.next')">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-10 h-10 text-gray-300 cursor-not-allowed" aria-disabled="true">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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