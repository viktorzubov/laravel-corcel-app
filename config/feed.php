<?php

return [
    'feeds' => [
        'main' => [
            'items' => [\App\Models\Post::class, 'getFeedItems'],

            'url' => '/feed',

            'title' => config('app.name', 'Blog'),
            'description' => 'The latest posts from ' . config('app.name', 'Blog'),
            'language' => 'en-US',

            'image' => '',

            /*
             * Acceptable values are 'rss', 'atom', or 'json'.
             */
            'format' => 'rss',

            'view' => 'feed::rss',

            'type' => '',

            'contentType' => '',
        ],
    ],
];
