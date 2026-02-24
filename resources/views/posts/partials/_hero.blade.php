{{-- Full-bleed hero --}}
<div class="-mx-18 -mt-10 relative overflow-hidden h-[460px]">
    <img src="{{ $post->thumbnailUrl(1600, 700) }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-black/10"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-end pb-16 px-8 text-center text-white">
        @if (!empty($post->terms['category']))
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                @foreach (($post->terms['category'] ?? []) as $catSlug => $catName)
                    <a href="{{ route('category.show', $catSlug) }}"
                       class="text-xs font-semibold uppercase tracking-wide bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full px-3 py-1 transition-colors">
                        {{ $catName }}
                    </a>
                @endforeach
            </div>
        @endif
        <h1 class="text-4xl lg:text-5xl font-bold leading-tight max-w-4xl mb-6 drop-shadow">{{ $post->title }}</h1>
        @if ($post->author)
            <div class="flex items-center gap-2.5 text-white/90 text-sm">
                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($post->author->user_email))) }}?s=36&d=mp"
                     alt="{{ $post->author->display_name }}"
                     class="size-8 rounded-full ring-2 ring-white/40">
                <a href="{{ route('author.show', $post->author->slug) }}" class="hover:text-white transition-colors">
                    {{ $post->author->display_name }}
                </a>
            </div>
        @endif
    </div>
</div>
