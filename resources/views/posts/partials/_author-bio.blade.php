{{-- Author bio card --}}
@if ($post->author)
    @php
        $authorPostCount = \App\Models\Post::type('post')->published()->where('post_author', $post->author->ID)->count();
        $authorBio = $post->author->getMeta('description');
    @endphp
    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-800 flex gap-5 items-start">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($post->author->user_email))) }}?s=72&d=mp"
             alt="{{ $post->author->display_name }}"
             loading="lazy"
             class="size-16 rounded-full shrink-0 ring-2 ring-gray-100 dark:ring-gray-800">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                <a href="{{ route('author.show', $post->author->slug) }}"
                   class="text-base font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ $post->author->display_name }}
                </a>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $authorPostCount }} {{ Str::plural('post', $authorPostCount) }}</span>
            </div>
            @if ($authorBio)
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-2">{{ $authorBio }}</p>
            @endif
            <a href="{{ route('author.show', $post->author->slug) }}"
               class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                View all posts &rarr;
            </a>
        </div>
    </div>
@endif
