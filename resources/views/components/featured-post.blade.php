<a href="{{ route('posts.show', $post->slug) }}"
   class="group block mb-10 rounded-2xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
    <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="overflow-hidden max-h-72 md:max-h-none">
            <img src="{{ $post->thumbnailUrl(1200, 600) }}" alt="{{ $post->title }}"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
        </div>
        <div class="p-7 flex flex-col justify-center gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach (($post->terms['category'] ?? []) as $catSlug => $catName)
                    <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">{{ $catName }}</span>
                @endforeach
                <span class="text-xs font-semibold text-gray-300 uppercase tracking-wide">Featured</span>
            </div>
            <h2 class="text-2xl font-bold leading-snug group-hover:text-indigo-600 transition-colors">
                {{ $post->title }}
            </h2>
            @if ($post->excerpt)
                <p class="text-sm text-gray-500 line-clamp-3 leading-relaxed">{{ strip_tags($post->excerpt) }}</p>
            @endif
            @php $views = $post->viewCount(); @endphp
            <div class="flex items-center gap-3 text-xs text-gray-400 mt-1">
                <span>{{ $post->post_date?->format('M j, Y') }}</span>
                <span>{{ $post->readTimeMinutes() }} min read</span>
                @if ($views > 0)
                    <span class="flex items-center gap-1">
                        <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ number_format($views) }}
                    </span>
                @endif
                @if ($post->author)
                    <span>{{ $post->author->display_name }}</span>
                @endif
            </div>
        </div>
    </div>
</a>
