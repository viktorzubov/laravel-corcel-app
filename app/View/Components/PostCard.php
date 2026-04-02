<?php

namespace App\View\Components;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PostCard extends Component
{
    public function __construct(
        public Post $post,
        public bool $showCategories = true,
        public bool $showExcerpt = true,
        public bool $showReadTime = true,
        public bool $showViews = true,
        public bool $showCommentCount = true,
        public bool $showAuthor = true,
        public bool $compact = false,
    ) {}

    public function render(): View
    {
        return view('components.post-card');
    }
}
