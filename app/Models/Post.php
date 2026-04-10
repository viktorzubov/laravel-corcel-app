<?php

namespace App\Models;

use Corcel\Model\Meta\PostMeta;
use Corcel\Model\Post as Corcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function scopeRelatedTo(Builder $query, self $post, int $limit = 3): Builder
    {
        $firstCategory = array_key_first($post->terms['category'] ?? []);

        return $query->type('post')
            ->published()
            ->with(['thumbnail'])
            ->when($firstCategory, fn ($q) => $q->taxonomy('category', $firstCategory))
            ->where('ID', '!=', $post->ID)
            ->latest('post_date')
            ->limit($limit);
    }

    public function scopePreviousTo(Builder $query, self $post): Builder
    {
        return $query->type('post')
            ->published()
            ->where('post_date', '<', $post->post_date)
            ->latest('post_date');
    }

    public function scopeNextTo(Builder $query, self $post): Builder
    {
        return $query->type('post')
            ->published()
            ->where('post_date', '>', $post->post_date)
            ->oldest('post_date');
    }

    public function scopeForAuthor(Builder $query, mixed $author): Builder
    {
        return $query->where('post_author', $author->ID);
    }

    public function readTimeMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200));
    }

    public function thumbnailUrl(int $width = 800, int $height = 450): string
    {
        return $this->thumbnail ?: asset('images/default-thumbnail.jpg');
    }

    public function viewCountMeta(): HasMany
    {
        return $this->hasMany(PostMeta::class, 'post_id')
            ->where('meta_key', 'post_views_count');
    }

    public function viewCount(): int
    {
        if ($this->relationLoaded('viewCountMeta')) {
            return (int) ($this->viewCountMeta->first()?->meta_value ?? 0);
        }

        return (int) ($this->getMeta('post_views_count') ?? 0);
    }
}
