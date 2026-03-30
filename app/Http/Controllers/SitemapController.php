<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Corcel\Model\Taxonomy;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): \Illuminate\Http\Response
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(route('posts.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        Post::type('post')
            ->published()
            ->latest('post_modified')
            ->get(['post_name', 'post_modified'])
            ->each(function (Post $post) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('posts.show', $post->post_name))
                        ->setLastModificationDate($post->post_modified)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            });

        Post::type('page')
            ->published()
            ->latest('post_modified')
            ->get(['post_name', 'post_modified'])
            ->each(function (Post $post) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('page.show', $post->post_name))
                        ->setLastModificationDate($post->post_modified)
                        ->setPriority(0.6)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                );
            });

        Taxonomy::category()
            ->with('term')
            ->get()
            ->each(function (Taxonomy $category) use ($sitemap): void {
                if ($category->term?->slug) {
                    $sitemap->add(
                        Url::create(route('category.show', $category->term->slug))
                            ->setPriority(0.5)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });

        Taxonomy::where('taxonomy', 'post_tag')
            ->with('term')
            ->get()
            ->each(function (Taxonomy $tag) use ($sitemap): void {
                if ($tag->term?->slug) {
                    $sitemap->add(
                        Url::create(route('tag.show', $tag->term->slug))
                            ->setPriority(0.4)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });

        return $sitemap->toResponse(request());
    }
}
