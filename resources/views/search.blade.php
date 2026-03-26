@extends('layout.app')

@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">Search</h1>
        <form action="{{ route('search') }}" method="GET" role="search" class="flex gap-3 max-w-xl">
            <input type="search" name="q" value="{{ $query }}" placeholder="Search posts…" autofocus
                   class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
            <button type="submit"
                    class="rounded-lg bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white px-5 py-2 text-sm font-medium transition-colors">
                Search
            </button>
        </form>
    </div>

    @if ($query)
        @if ($posts->isEmpty())
            <div class="py-16 text-center">
                <svg class="size-12 mx-auto mb-4 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <p class="text-lg font-semibold mb-1">No results for &ldquo;{{ $query }}&rdquo;</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">Try different keywords or browse all posts.</p>
                <a href="{{ route('posts.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Browse all posts &rarr;</a>
            </div>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">
                {{ $posts->total() }} result{{ $posts->total() === 1 ? '' : 's' }} for <strong class="text-gray-700 dark:text-gray-300">{{ $query }}</strong>
            </p>

            <div class="flex flex-col gap-4">
                @foreach ($posts as $post)
                    <article class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow flex gap-5 p-5">
                        @php $postImg = $post->thumbnailUrl(400, 280); @endphp
                        <a href="{{ route('posts.show', $post->slug) }}" class="shrink-0">
                            <img src="{{ $postImg }}" alt="{{ $post->title }}" class="w-28 h-20 object-cover rounded-lg">
                        </a>
                        <div class="flex flex-col flex-1 gap-1.5 min-w-0">
                            <div class="flex flex-wrap gap-2">
                                @foreach (($post->terms['category'] ?? []) as $catSlug => $catName)
                                    <a href="{{ route('posts.index', ['category' => $catSlug]) }}"
                                       class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide hover:underline">
                                        {{ $catName }}
                                    </a>
                                @endforeach
                            </div>
                            <h2 class="text-base font-semibold leading-snug">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {!! $highlighter->highlight($post->title) !!}
                                </a>
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">
                                {!! $highlighter->highlight($highlighter->snippet($post->excerpt ?: $post->content)) !!}
                            </p>
                            <div class="mt-auto flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                <span>{{ $post->post_date?->format('M j, Y') }}</span>
                                <span>{{ $post->readTimeMinutes() }} min read</span>
                                @if ($post->author)
                                    <a href="{{ route('author.show', $post->author->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ $post->author->display_name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    @endif
@endsection
