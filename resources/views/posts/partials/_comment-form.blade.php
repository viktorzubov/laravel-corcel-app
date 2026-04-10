{{-- Leave a comment form --}}
<div id="comment-form" class="mt-10 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
    <h3 class="text-base font-semibold mb-5">Leave a comment</h3>

    {{-- Replying-to banner (hidden until Reply is clicked) --}}
    <div id="reply-banner" class="hidden mb-4 flex items-center justify-between rounded-lg bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 px-4 py-2 text-sm text-indigo-700 dark:text-indigo-300">
        <span>Replying to <strong id="reply-author-name"></strong></span>
        <button type="button" id="cancel-reply" class="text-xs underline hover:no-underline">Cancel</button>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.comments.store', $post->post_name) }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="parent_id" id="comment-parent-id" value="0">

        @auth
            {{-- Pass name and email from the authenticated user silently --}}
            <input type="hidden" name="author" value="{{ auth()->user()->name }}">
            <input type="hidden" name="email" value="{{ auth()->user()->email }}">

            <div class="flex items-center justify-between rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                <span>Commenting as <strong class="text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</strong></span>
                <a href="{{ route('profile.edit') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Edit profile</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="author" name="author" value="{{ old('author') }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors">
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Log in</a>
                to skip filling in your details next time.
            </p>
        @endauth

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comment <span class="text-red-500">*</span></label>
            <textarea id="content" name="content" rows="4" required
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors resize-y">{{ old('content') }}</textarea>
        </div>
        <button type="submit"
                class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors">
            Post Comment
        </button>
    </form>
</div>
