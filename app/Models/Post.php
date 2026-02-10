<?php

namespace App\Models;

use Corcel\Model\Post as Corcel;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Corcel implements Feedable
{
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id(route('posts.show', $this->post_name))
            ->title($this->post_title)
            ->summary(strip_tags($this->post_excerpt ?: mb_substr(strip_tags($this->post_content), 0, 280)))
            ->updated($this->post_modified ?? $this->post_date)
            ->link(route('posts.show', $this->post_name))
            ->authorName($this->author?->display_name ?? config('app.name'));
    }

    public static function getFeedItems()
    {
        return static::type('post')
            ->published()
            ->with(['author'])
            ->latest('post_date')
            ->limit(20)
            ->get();
    }

    public function scopeOfType($query, string $type = 'post')
    {
        return $query->type($type);
    }

    public function readTimeMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200));
    }

    public function thumbnailUrl(int $width = 800, int $height = 450): string
    {
        return $this->thumbnail ?: "https://picsum.photos/seed/{$this->post_name}/{$width}/{$height}";
    }

    public function viewCount(): int
    {
        return (int) ($this->getMeta('post_views_count') ?? 0);
    }
}
