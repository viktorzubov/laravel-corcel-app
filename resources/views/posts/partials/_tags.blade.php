{{-- Tags --}}
@if (!empty($post->terms['tag']))
    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-wrap gap-2 items-center">
        <span class="text-sm text-gray-400">Tags:</span>
        @foreach ($post->terms['tag'] as $tagSlug => $tagName)
            <a href="{{ route('tag.show', $tagSlug) }}"
               class="text-xs bg-gray-100 text-gray-600 rounded-full px-3 py-1 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                #{{ $tagName }}
            </a>
        @endforeach
    </div>
@endif
