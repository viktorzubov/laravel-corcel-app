<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void
    {
        $this->clearCaches();
    }

    public function updated(Post $post): void
    {
        $this->clearCaches();
    }

    public function deleted(Post $post): void
    {
        $this->clearCaches();
    }

    private function clearCaches(): void
    {
        Cache::forget('post_categories');
        Cache::forget('sitemap_xml');
    }
}
