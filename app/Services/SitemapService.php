<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Corcel\Model\Taxonomy;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    public function render(): string
    {
        return Cache::remember('sitemap_xml', 86400, function (): string {
            $sitemap = Sitemap::create()
                ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
                ->add(Url::create(route('posts.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

            $this->addPosts($sitemap);
            $this->addPages($sitemap);
            $this->addCategories($sitemap);
            $this->addTags($sitemap);

            return $sitemap->render();
        });
    }

    private function addPosts(Sitemap $sitemap): void
    {
        Post::type('post')->published()->latest('post_modified')
            ->get(['post_name', 'post_modified'])
            ->each(fn (Post $post) => $sitemap->add(
                Url::create(route('posts.show', $post->post_name))
                    ->setLastModificationDate($post->post_modified)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ));
    }

    private function addPages(Sitemap $sitemap): void
    {
        Post::type('page')->published()->latest('post_modified')
            ->get(['post_name', 'post_modified'])
            ->each(fn (Post $post) => $sitemap->add(
                Url::create(route('page.show', $post->post_name))
                    ->setLastModificationDate($post->post_modified)
                    ->setPriority(0.6)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ));
    }

    private function addCategories(Sitemap $sitemap): void
    {
        Taxonomy::category()->with('term')->get()
            ->each(function (Taxonomy $category) use ($sitemap): void {
                if ($category->term?->slug) {
                    $sitemap->add(
                        Url::create(route('category.show', $category->term->slug))
                            ->setPriority(0.5)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });
    }

    private function addTags(Sitemap $sitemap): void
    {
        Tag::with('term')->get()
            ->each(function (Tag $tag) use ($sitemap): void {
                if ($tag->term?->slug) {
                    $sitemap->add(
                        Url::create(route('tag.show', $tag->term->slug))
                            ->setPriority(0.4)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                }
            });
    }
}
