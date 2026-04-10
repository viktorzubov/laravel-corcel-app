@extends('layout.app')

@section('title', 'Page Not Found')

@section('content')
    @php
        $recentPosts = \App\Models\Post::type('post')
            ->published()
            ->with(['thumbnail', 'author'])
            ->latest('post_date')
            ->limit(3)
            ->get();
    @endphp

    <div class="max-w-2xl mx-auto text-center py-16">
        <p class="text-8xl font-black text-indigo-100 select-none leading-none">404</p>
        <h1 class="text-2xl font-bold mt-2 mb-3">Page not found</h1>
        <p class="text-gray-500 mb-8">
            The page you're looking for doesn't exist or may have been moved.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('home') }}"
               class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors">
                Go home
            </a>
            <a href="{{ route('posts.index') }}"
               class="px-5 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-medium hover:border-indigo-400 hover:text-indigo-600 transition-colors">
                Browse posts
            </a>
        </div>
    </div>

    @if ($recentPosts->isNotEmpty())
        <div class="max-w-3xl mx-auto mt-8 pt-10 border-t border-gray-200">
            <h2 class="text-base font-semibold text-gray-500 mb-5 text-center uppercase tracking-wide text-xs">Recent posts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach ($recentPosts as $post)
                    <a href="{{ route('posts.show', $post->slug) }}"
                       class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <img src="{{ $post->thumbnailUrl(600, 300) }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-36 object-cover group-hover:opacity-90 transition-opacity">
                        <div class="p-4 flex flex-col flex-1 gap-1">
                            <h3 class="text-sm font-semibold leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </h3>
                            <span class="text-xs text-gray-400 mt-auto pt-2">{{ $post->post_date?->format('M j, Y') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
