@extends('layout.app')

@use('Illuminate\Support\Str')

@section('title', $post->title)
@section('description', strip_tags($post->excerpt) ?: strip_tags(Str::limit($post->content, 160)))
@section('og_type', 'article')
@section('canonical', route('posts.show', $post->slug))
@section('og_image', $post->thumbnail ?: 'https://picsum.photos/seed/' . $post->post_name . '/1200/630')

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github.min.css" media="(prefers-color-scheme: light), not all">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css" media="(prefers-color-scheme: dark)">
    <script>
        // Override hljs theme based on current dark mode class, not media query
        (function () {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('link[href*="highlight.js"]').forEach(l => l.disabled = true);
            const theme = isDark ? 'github-dark' : 'github';
            const link = document.querySelector(`link[href*="${theme}.min.css"]`);
            if (link) link.disabled = false;
        })();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js" defer></script>
    <script>document.addEventListener('DOMContentLoaded', () => hljs.highlightAll());</script>
    <style>
        html { scroll-behavior: smooth; }
        /* Push anchored h2 headings below the sticky header + breathing room */
        #post-content h2 { scroll-margin-top: 5rem; }
    </style>
@endpush

@push('body_start')
    <div id="reading-progress"
         class="fixed top-0 left-0 h-[3px] w-0 bg-indigo-500 z-50 transition-none pointer-events-none"
         role="progressbar" aria-hidden="true">
    </div>
    {{-- Back to top button --}}
    <button id="back-to-top"
            onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
            aria-label="Back to top"
            class="fixed bottom-6 right-6 z-40 size-10 flex items-center justify-center rounded-full bg-indigo-600 text-white shadow-lg opacity-0 translate-y-4 pointer-events-none transition-all duration-300 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
        </svg>
    </button>
@endpush

@section('content')

@include('posts.partials._hero')
@include('posts.partials._meta-bar')

<div class="xl:grid xl:grid-cols-[1fr_280px] xl:gap-12 xl:items-start">
    <article class="min-w-0">

        {{-- Mobile / tablet TOC --}}
        @if (count($headings) >= 2)
            <x-post-toc :headings="$headings" variant="mobile" />
        @endif

        {{-- Content --}}
        <div class="prose prose-gray dark:prose-invert max-w-none" id="post-content">
            {!! $processedContent !!}
        </div>


        @include('posts.partials._tags')
        @include('posts.partials._author-bio')
        @include('posts.partials._post-nav')
        @include('posts.partials._related')

        <div class="mt-10">
            <a href="{{ route('posts.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to posts</a>
        </div>

        @if ($post->comment_status === 'open')
            <section id="comments" class="mt-16 pt-10 border-t border-gray-200 dark:border-gray-800">
                @include('posts.partials._comments')
                @include('posts.partials._comment-form')
            </section>
        @endif

    </article>

    {{-- Sticky Contents sidebar (xl only) --}}
    @if (count($headings) >= 2)
        <x-post-toc :headings="$headings" variant="sidebar" />
    @endif
</div>

@endsection
