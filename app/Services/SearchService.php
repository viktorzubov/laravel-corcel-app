<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function search(string $query, int $perPage = 9): LengthAwarePaginator
    {
        return Post::type('post')
            ->published()
            ->with(['thumbnail', 'author', 'taxonomies.term'])
            ->where(function ($q) use ($query): void {
                $q->where('post_title', 'like', "%{$query}%")
                    ->orWhere('post_content', 'like', "%{$query}%")
                    ->orWhere('post_excerpt', 'like', "%{$query}%");
            })
            ->latest('post_date')
            ->paginate($perPage)
            ->withQueryString();
    }
}
