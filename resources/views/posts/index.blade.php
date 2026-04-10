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
                      {{ ! $activeCategory ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-200 text-gray-600 hover:border-indigo-400 hover:text-indigo-600' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('posts.index', ['category' => $cat->slug]) }}"
                   class="text-sm px-4 py-1.5 rounded-full border transition-colors
                          {{ $activeCategory === $cat->slug ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-200 text-gray-600 hover:border-indigo-400 hover:text-indigo-600' }}">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-60 text-xs">{{ $cat->count }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    {{-- Featured / hero post --}}
    @if ($featured)
        <x-featured-post :post="$featured" />
    @endif

    @if ($posts->isEmpty() && ! $featured)
        <p class="text-gray-500">No posts found.</p>
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
