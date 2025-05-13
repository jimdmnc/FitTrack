@isset($announcements)
<div class="announcements-section py-16 bg-gradient-to-b from-gray-900 to-black">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-white">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600">
                Latest Updates
            </span>
        </h2>
        
        <div class="announcements-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($announcements as $announcement)
            <div class="announcement-card relative bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform transition-all duration-500 hover:scale-105 hover:shadow-red-500/20">
                <!-- Decorative element -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-500 to-red-600"></div>
                
                <!-- Card content -->
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-white">{{ $announcement->title ?? 'Announcement' }}</h3>
                        @if($announcement->type ?? false)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{
                            $announcement->type === 'Event' ? 'bg-green-600 text-green-100' :
                            ($announcement->type === 'Maintenance' ? 'bg-blue-600 text-blue-100' :
                            ($announcement->type === 'Update' ? 'bg-orange-600 text-orange-100' :
                            'bg-gray-600 text-white'))
                            }}">
                            {{ $announcement->type }}
                        </span>

                        @endif
                    </div>
                    
                    <p class="text-gray-300 mb-6 line-clamp-4">{{ $announcement->content ?? 'No content available' }}</p>
                    
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-orange-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $announcement->schedule ? $announcement->schedule->format('M d, Y') : 'No date' }}
                        </span>
                        <button class="read-more-btn text-red-400 hover:text-red-300 font-medium transition-colors">
                            Read More →
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($announcements->isEmpty())
        <div class="text-center py-12">
            <div class="inline-block p-4 bg-gray-800 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-gray-400">No announcements available at this time</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endisset