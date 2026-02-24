{{-- Related posts --}}
@if ($related->isNotEmpty())
    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold mb-6">Related Posts</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach ($related as $relatedPost)
                <x-post-card :post="$relatedPost" compact />
            @endforeach
        </div>
    </div>
@endif
