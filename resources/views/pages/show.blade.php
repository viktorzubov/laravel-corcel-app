@extends('layout.app')

@section('title', $page->title)
@section('canonical', route('page.show', $page->slug))

@section('content')
    <article class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">{{ $page->title }}</h1>

        <div class="prose prose-gray dark:prose-invert max-w-none">
            {!! $page->content !!}
        </div>
    </article>
@endsection
