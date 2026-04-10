{{-- Comments list --}}
<h2 class="text-xl font-bold mb-8">
    {{ $commentCount }} {{ Str::plural('Comment', $commentCount) }}
</h2>

@if (session('comment_success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
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
                    <span class="text-sm font-semibold text-gray-900">{{ $comment->comment_author }}</span>
                    <span class="text-xs text-gray-400">{{ $comment->comment_date?->diffForHumans() }}</span>
                </div>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {{ $comment->comment_content }}
                </div>
                <button type="button"
                        data-reply-to="{{ $comment->comment_ID }}"
                        data-reply-author="{{ $comment->comment_author }}"
                        class="reply-btn mt-2 text-xs text-indigo-600 hover:underline">
                    Reply
                </button>
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
                        <span class="text-sm font-semibold text-gray-900">{{ $reply->comment_author }}</span>
                        <span class="text-xs text-gray-400">{{ $reply->comment_date?->diffForHumans() }}</span>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {{ $reply->comment_content }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <p class="text-sm text-gray-400 mb-8">No comments yet. Be the first!</p>
@endforelse
