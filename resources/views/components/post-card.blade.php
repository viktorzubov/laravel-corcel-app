<article class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
    <a href="{{ route('posts.show', $post->slug) }}">
        <img src="{{ $post->thumbnailUrl($compact ? 600 : 800, $compact ? 300 : 450) }}"
             alt="{{ $post->title }}"
             class="w-full {{ $compact ? 'h-36' : 'h-48' }} object-cover">
    </a>
    <div class="{{ $compact ? 'p-4 gap-2' : 'p-5 gap-3' }} flex flex-col flex-1">

        @if ($showCategories && ! $compact && ! empty($post->terms['category']))
            <div class="flex flex-wrap gap-2">
                @foreach (($post->terms['category'] ?? []) as $catSlug => $catName)
                    <a href="{{ route('posts.index', ['category' => $catSlug]) }}"
                       class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide hover:underline">
                        {{ $catName }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($compact)
            <h3 class="text-sm font-semibold leading-snug">
                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ $post->title }}
                </a>
            </h3>
        @else
            <h2 class="text-lg font-semibold leading-snug">
                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ $post->title }}
                </a>
            </h2>
        @endif

        @if ($showExcerpt && ! $compact && $post->excerpt)
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3">{{ strip_tags($post->excerpt) }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
            <span>{{ $post->post_date?->format('M j, Y') }}</span>

            @if (! $compact)
                @php $views = $post->viewCount(); @endphp
                <div class="flex items-center gap-3">
                    @if ($showReadTime)
                        <span>{{ $post->readTimeMinutes() }} min read</span>
                    @endif
                    @if ($showViews && $views > 0)
                        <span class="flex items-center gap-1">
                            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            {{ number_format($views) }}
                        </span>
                    @endif
                    @if ($showCommentCount && $post->comment_count > 0)
                        <span class="flex items-center gap-1">
                            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.836L3 20l1.09-3.635C3.392 15.024 3 13.553 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ $post->comment_count }}
                        </span>
                    @endif
                    @if ($showAuthor && $post->author)
                        <a href="{{ route('author.show', $post->author->slug) }}"
                           class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            {{ $post->author->display_name }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</article>
