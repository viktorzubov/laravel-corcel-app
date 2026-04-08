<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CreatesWordPressData
{
    protected function setUpWordPressDatabase(): void
    {
        $schema = Schema::connection('wordpress');

        // Drop in reverse dependency order to avoid constraint issues
        $schema->dropIfExists('commentmeta');
        $schema->dropIfExists('comments');
        $schema->dropIfExists('term_relationships');
        $schema->dropIfExists('term_taxonomy');
        $schema->dropIfExists('terms');
        $schema->dropIfExists('postmeta');
        $schema->dropIfExists('posts');
        $schema->dropIfExists('usermeta');
        $schema->dropIfExists('users');

        $schema->create('users', function (Blueprint $table) {
            $table->id('ID');
            $table->string('user_login', 60)->default('');
            $table->string('user_pass', 255)->default('');
            $table->string('user_nicename', 50)->default('');
            $table->string('user_email', 100)->default('');
            $table->string('user_url', 100)->default('');
            $table->dateTime('user_registered')->default('2000-01-01 00:00:00');
            $table->string('user_activation_key', 255)->default('');
            $table->integer('user_status')->default(0);
            $table->string('display_name', 250)->default('');
        });

        $schema->create('usermeta', function (Blueprint $table) {
            $table->id('umeta_id');
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->string('meta_key', 255)->nullable()->index();
            $table->longText('meta_value')->nullable();
        });

        $schema->create('posts', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('post_author')->default(0);
            $table->dateTime('post_date')->default('2000-01-01 00:00:00');
            $table->dateTime('post_date_gmt')->default('2000-01-01 00:00:00');
            $table->longText('post_content');
            $table->text('post_title');
            $table->text('post_excerpt');
            $table->string('post_status', 20)->default('publish');
            $table->string('comment_status', 20)->default('open');
            $table->string('ping_status', 20)->default('open');
            $table->string('post_password', 255)->default('');
            $table->string('post_name', 200)->default('')->index();
            $table->text('to_ping');
            $table->text('pinged');
            $table->dateTime('post_modified')->default('2000-01-01 00:00:00');
            $table->dateTime('post_modified_gmt')->default('2000-01-01 00:00:00');
            $table->longText('post_content_filtered');
            $table->unsignedBigInteger('post_parent')->default(0);
            $table->string('guid', 255)->default('');
            $table->integer('menu_order')->default(0);
            $table->string('post_type', 20)->default('post');
            $table->string('post_mime_type', 100)->default('');
            $table->bigInteger('comment_count')->default(0);
        });

        $schema->create('postmeta', function (Blueprint $table) {
            $table->id('meta_id');
            $table->unsignedBigInteger('post_id')->default(0)->index();
            $table->string('meta_key', 255)->nullable()->index();
            $table->longText('meta_value')->nullable();
        });

        $schema->create('terms', function (Blueprint $table) {
            $table->id('term_id');
            $table->string('name', 200)->default('');
            $table->string('slug', 200)->default('')->index();
            $table->bigInteger('term_group')->default(0);
        });

        $schema->create('term_taxonomy', function (Blueprint $table) {
            $table->id('term_taxonomy_id');
            $table->unsignedBigInteger('term_id')->default(0)->index();
            $table->string('taxonomy', 32)->default('')->index();
            $table->longText('description');
            $table->unsignedBigInteger('parent')->default(0);
            $table->bigInteger('count')->default(0);
        });

        $schema->create('term_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('object_id')->default(0);
            $table->unsignedBigInteger('term_taxonomy_id')->default(0)->index();
            $table->integer('term_order')->default(0);
            $table->primary(['object_id', 'term_taxonomy_id']);
        });

        $schema->create('comments', function (Blueprint $table) {
            $table->id('comment_ID');
            $table->unsignedBigInteger('comment_post_ID')->default(0)->index();
            $table->text('comment_author');
            $table->string('comment_author_email', 100)->default('')->index();
            $table->string('comment_author_url', 200)->default('');
            $table->string('comment_author_IP', 100)->default('');
            $table->dateTime('comment_date')->default('2000-01-01 00:00:00');
            $table->dateTime('comment_date_gmt')->default('2000-01-01 00:00:00');
            $table->text('comment_content');
            $table->integer('comment_karma')->default(0);
            $table->string('comment_approved', 20)->default('1');
            $table->string('comment_agent', 255)->default('');
            $table->string('comment_type', 20)->default('comment');
            $table->unsignedBigInteger('comment_parent')->default(0);
            $table->unsignedBigInteger('user_id')->default(0);
        });

        $schema->create('commentmeta', function (Blueprint $table) {
            $table->id('meta_id');
            $table->unsignedBigInteger('comment_id')->default(0)->index();
            $table->string('meta_key', 255)->nullable()->index();
            $table->longText('meta_value')->nullable();
        });
    }

    protected function createPost(array $attrs = []): int
    {
        return DB::connection('wordpress')->table('posts')->insertGetId(array_merge([
            'post_author'           => 0,
            'post_date'             => now()->format('Y-m-d H:i:s'),
            'post_date_gmt'         => now()->utc()->format('Y-m-d H:i:s'),
            'post_content'          => '<p>Test content paragraph.</p>',
            'post_title'            => 'Test Post',
            'post_excerpt'          => '',
            'post_status'           => 'publish',
            'comment_status'        => 'open',
            'ping_status'           => 'open',
            'post_password'         => '',
            'post_name'             => 'test-post-'.uniqid(),
            'to_ping'               => '',
            'pinged'                => '',
            'post_modified'         => now()->format('Y-m-d H:i:s'),
            'post_modified_gmt'     => now()->utc()->format('Y-m-d H:i:s'),
            'post_content_filtered' => '',
            'post_parent'           => 0,
            'guid'                  => '',
            'menu_order'            => 0,
            'post_type'             => 'post',
            'post_mime_type'        => '',
            'comment_count'         => 0,
        ], $attrs));
    }

    protected function createUser(array $attrs = []): int
    {
        return DB::connection('wordpress')->table('users')->insertGetId(array_merge([
            'user_login'          => 'testuser',
            'user_pass'           => '',
            'user_nicename'       => 'testuser',
            'user_email'          => 'test@example.com',
            'user_url'            => '',
            'user_registered'     => now()->format('Y-m-d H:i:s'),
            'user_activation_key' => '',
            'user_status'         => 0,
            'display_name'        => 'Test User',
        ], $attrs));
    }

    protected function createCategory(string $name, string $slug): int
    {
        $termId = DB::connection('wordpress')->table('terms')->insertGetId([
            'name'       => $name,
            'slug'       => $slug,
            'term_group' => 0,
        ]);

        DB::connection('wordpress')->table('term_taxonomy')->insert([
            'term_id'     => $termId,
            'taxonomy'    => 'category',
            'description' => '',
            'parent'      => 0,
            'count'       => 0,
        ]);

        return $termId;
    }

    protected function createTag(string $name, string $slug): int
    {
        $termId = DB::connection('wordpress')->table('terms')->insertGetId([
            'name'       => $name,
            'slug'       => $slug,
            'term_group' => 0,
        ]);

        DB::connection('wordpress')->table('term_taxonomy')->insert([
            'term_id'     => $termId,
            'taxonomy'    => 'post_tag',
            'description' => '',
            'parent'      => 0,
            'count'       => 0,
        ]);

        return $termId;
    }

    protected function attachTermToPost(int $postId, int $termId, string $taxonomy): void
    {
        $termTaxonomyId = DB::connection('wordpress')
            ->table('term_taxonomy')
            ->where('term_id', $termId)
            ->where('taxonomy', $taxonomy)
            ->value('term_taxonomy_id');

        DB::connection('wordpress')->table('term_relationships')->insert([
            'object_id'        => $postId,
            'term_taxonomy_id' => $termTaxonomyId,
            'term_order'       => 0,
        ]);
    }
}
