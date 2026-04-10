@extends('layout.app')

@use('Illuminate\Support\Str')

@section('title', $author->display_name . ' — Author')
@section('description', $author->getMeta('description') ?: 'Posts by ' . $author->display_name)
@section('canonical', route('author.show', $author->slug))

@section('content')

    {{-- Author hero --}}
    <div class="max-w-2xl mx-auto text-center mb-12">
        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($author->user_email))) }}?s=128&d=mp"
             loading="lazy"
             alt="{{ $author->display_name }}"
             class="size-24 rounded-full mx-auto mb-4 ring-4 ring-white dark:ring-gray-900 shadow">

        <h1 class="text-2xl font-bold mb-1">{{ $author->display_name }}</h1>

        @if ($author->getMeta('description'))
            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-4 max-w-lg mx-auto">
                {{ $author->getMeta('description') }}
            </p>
        @endif

        <div class="flex items-center justify-center gap-6 text-sm text-gray-400 dark:text-gray-500">
            <span>
                <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $posts->total() }}</strong>
                {{ Str::plural('post', $posts->total()) }}
            </span>
            <span>Member since {{ \Carbon\Carbon::parse($author->user_registered)->format('F Y') }}</span>
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-800 mb-10"></div>

    {{-- Posts grid --}}
    @if ($posts->isEmpty())
        <p class="text-center text-gray-400 dark:text-gray-500">No published posts yet.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($posts as $post)
                <x-post-card :post="$post" :show-author="false" :show-views="false" />
            @endforeach
        </div>

        @if ($posts->hasPages())
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    @endif

@endsection
