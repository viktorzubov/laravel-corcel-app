{{-- Comments list --}}
<h2 class="text-xl font-bold mb-8">
    {{ $commentCount }} {{ Str::plural('Comment', $commentCount) }}
</h2>

@if (session('comment_success'))
    <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
        {{ session('comment_success') }}
    </div>
@endif

@forelse ($comments as $comment)
    <div class="mb-8">
        <div class="flex gap-4">
            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($comment->comment_author_email))) }}?s=40&d=mp"
                 loading="lazy"
                 alt="{{ $comment->comment_author }}"
                 class="size-10 rounded-full shrink-0">
            <div class="flex-1">
                <div class="flex items-baseline gap-3 mb-1">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $comment->comment_author }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $comment->comment_date?->diffForHumans() }}</span>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $comment->comment_content }}
                </div>
            </div>
        </div>
        @foreach ($comment->replies->where('comment_approved', '1') as $reply)
            <div class="mt-4 ml-14 flex gap-4">
                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($reply->comment_author_email))) }}?s=36&d=mp"
                     loading="lazy"
                     alt="{{ $reply->comment_author }}"
                     class="size-9 rounded-full shrink-0">
                <div class="flex-1">
                    <div class="flex items-baseline gap-3 mb-1">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $reply->comment_author }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $reply->comment_date?->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $reply->comment_content }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <p class="text-sm text-gray-400 dark:text-gray-500 mb-8">No comments yet. Be the first!</p>
@endforelse
