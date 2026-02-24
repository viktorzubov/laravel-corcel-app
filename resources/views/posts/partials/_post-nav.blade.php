{{-- Previous / Next navigation --}}
@if ($previousPost || $nextPost)
    <nav class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-800 grid grid-cols-2 gap-4">
        <div>
            @if ($previousPost)
                <a href="{{ route('posts.show', $previousPost->slug) }}"
                   class="group flex flex-col gap-1 text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Previous
                    </span>
                    <span class="font-medium leading-snug line-clamp-2">{{ $previousPost->title }}</span>
                </a>
            @endif
        </div>
        <div class="text-right">
            @if ($nextPost)
                <a href="{{ route('posts.show', $nextPost->slug) }}"
                   class="group flex flex-col gap-1 items-end text-sm hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                        Next
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                    <span class="font-medium leading-snug line-clamp-2">{{ $nextPost->title }}</span>
                </a>
            @endif
        </div>
    </nav>
@endif
