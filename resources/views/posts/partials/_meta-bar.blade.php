{{-- Floating meta bar --}}
<div class="relative z-10 -mt-8 mb-10 bg-white rounded-2xl shadow-lg border border-gray-100 px-6 py-3.5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500">
    {{-- Read time --}}
    <span class="flex items-center gap-1.5 shrink-0">
        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        {{ $post->readTimeMinutes() }} min read
    </span>
    <span class="hidden sm:block text-gray-200 select-none">|</span>

    {{-- Categories --}}
    @if (!empty($post->terms['category']))
        <span class="flex flex-wrap gap-2 shrink-0">
            @foreach (($post->terms['category'] ?? []) as $catSlug => $catName)
                <a href="{{ route('category.show', $catSlug) }}"
                   class="text-indigo-600 hover:underline font-medium">{{ $catName }}</a>
            @endforeach
        </span>
        <span class="hidden sm:block text-gray-200 select-none">|</span>
    @endif

    {{-- Published date --}}
    <span class="flex items-center gap-1.5 shrink-0">
        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Published: {{ $post->post_date?->format('M j, Y') }}
    </span>
    <span class="hidden sm:block text-gray-200 select-none">|</span>

    {{-- Comments --}}
    @if ($commentCount > 0)
        <a href="#comments" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors shrink-0">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.836L3 20l1.09-3.635C3.392 15.024 3 13.553 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            {{ $commentCount }} {{ Str::plural('comment', $commentCount) }}
        </a>
    @else
        <a href="#comments" class="hover:text-indigo-600 transition-colors shrink-0">No comments</a>
    @endif
    <span class="hidden sm:block text-gray-200 select-none">|</span>

    {{-- Views --}}
    <span class="flex items-center gap-1.5 shrink-0">
        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ number_format($viewCount) }}
    </span>
    <span class="hidden sm:block text-gray-200 select-none">|</span>

    {{-- Share --}}
    <span class="flex items-center gap-2 shrink-0">
        <span class="text-xs font-medium text-gray-400">Share:</span>
        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
           target="_blank" rel="noopener noreferrer"
           class="p-1.5 rounded-full bg-gray-100 hover:bg-black hover:text-white transition-colors">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.254 5.622L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer"
           class="p-1.5 rounded-full bg-gray-100 hover:bg-[#0077B5] hover:text-white transition-colors">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
        <button type="button" onclick="copyShareLink(this, '{{ route('posts.show', $post->slug) }}')"
                class="p-1.5 rounded-full bg-gray-100 hover:bg-indigo-600 hover:text-white transition-colors" aria-label="Copy link">
            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m-.758-4.9a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </button>
    </span>
</div>
<script>
    function copyShareLink(btn, url) {
        navigator.clipboard.writeText(url).then(() => {
            btn.classList.add('bg-green-600', 'text-white');
            btn.classList.remove('bg-gray-100', 'dark:bg-gray-800');
            setTimeout(() => {
                btn.classList.remove('bg-green-600', 'text-white');
                btn.classList.add('bg-gray-100');
            }, 2000);
        });
    }
</script>
