<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostService
{
    public function incrementViewCount(Post $post): int
    {
        $count = $post->viewCount() + 1;
        $post->saveMeta('post_views_count', $count);

        return $count;
    }

    public function processContent(string $content): string
    {
        return preg_replace_callback(
            '/<h2([^>]*)>(.*?)<\/h2>/is',
            function (array $m): string {
                $id = Str::slug(strip_tags($m[2]));

                return '<h2'.$m[1].' id="'.$id.'">'.$m[2].'</h2>';
            },
            $content
        );
    }

    public function extractHeadings(string $content): array
    {
        $headings = [];

        preg_replace_callback(
            '/<h2[^>]*>(.*?)<\/h2>/is',
            function (array $m) use (&$headings): string {
                $text = strip_tags($m[1]);
                $headings[] = ['id' => Str::slug($text), 'text' => $text];

                return $m[0];
            },
            $content
        );

        return $headings;
    }

    public function shareUrl(Post $post): string
    {
        return urlencode(route('posts.show', $post->slug));
    }

    public function shareTitle(Post $post): string
    {
        return urlencode($post->title);
    }
}
