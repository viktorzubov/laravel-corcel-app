@extends('layout.app')

@section('title', 'Posts')

@section('content')
    <div class="flex items-baseline justify-between mb-6">
        <h1 class="text-3xl font-bold">Latest Posts</h1>
    </div>

    {{-- Topic filter bar --}}
    @if ($categories->isNotEmpty())
        <nav class="flex flex-wrap gap-2 mb-8" aria-label="Filter by category">
            <a href="{{ route('posts.index') }}"
               class="text-sm px-4 py-1.5 rounded-full border transition-colors
                      {{ ! $activeCategory ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('posts.index', ['category' => $cat->slug]) }}"
                   class="text-sm px-4 py-1.5 rounded-full border transition-colors
                          {{ $activeCategory === $cat->slug ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-60 text-xs">{{ $cat->count }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    {{-- Featured / hero post --}}
    @if ($featured)
        <a href="{{ route('posts.show', $featured->slug) }}"
           class="group block mb-10 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="grid grid-cols-1 md:grid-cols-2">
                    @php $featuredImg = $featured->thumbnailUrl(1200, 600); @endphp
                <div class="overflow-hidden max-h-72 md:max-h-none">
                    <img src="{{ $featuredImg }}" alt="{{ $featured->title }}"
                         class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                </div>
                <div class="p-7 flex flex-col justify-center gap-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach (($featured->terms['category'] ?? []) as $catSlug => $catName)
                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">{{ $catName }}</span>
                        @endforeach
                        <span class="text-xs font-semibold text-gray-300 dark:text-gray-600 uppercase tracking-wide">Featured</span>
                    </div>
                    <h2 class="text-2xl font-bold leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $featured->title }}
                    </h2>
                    @if ($featured->excerpt)
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">{{ strip_tags($featured->excerpt) }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <span>{{ $featured->post_date?->format('M j, Y') }}</span>
                        <span>{{ $featured->readTimeMinutes() }} min read</span>
                        @php $featuredViews = $featured->viewCount(); @endphp
                        @if ($featuredViews > 0)
                            <span class="flex items-center gap-1">
                                <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                {{ number_format($featuredViews) }}
                            </span>
                        @endif
                        @if ($featured->author)
                            <span>{{ $featured->author->display_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    @endif

    @if ($posts->isEmpty() && ! $featured)
        <p class="text-gray-500 dark:text-gray-400">No posts found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($posts as $post)
                <x-post-card :post="$post" />
            @endforeach
        </div>

        @if ($posts->hasPages())
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    @endif
@endsection
