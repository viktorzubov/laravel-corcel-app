@extends('layout.app')

@section('title', '#' . $tag->name)
@section('canonical', route('tag.show', $tag->slug))

@section('content')
    <div class="mb-8">
        <a href="{{ route('posts.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; All posts</a>
        <h1 class="text-3xl font-bold mt-3">
            <span class="text-gray-400 dark:text-gray-500 font-normal">#</span>{{ $tag->name }}
        </h1>
    </div>

    @if ($posts->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">No posts with this tag.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($posts as $post)
                <x-post-card :post="$post" :show-categories="false" :show-read-time="false" :show-views="false" :show-comment-count="false" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
@endsection
