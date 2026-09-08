<?php

namespace App\View\Composers;

use App\Support\HeroImage;
use Roots\Acorn\View\Composer;

class Post extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Resolve invokable composer methods to plain values.
     * Acorn skips extractPublicMethods() when with() is non-empty, so every
     * blade variable this composer owns must be listed here.
     */
    public function with(): array
    {
        return [
            'title' => $this->title(),
            'pagination' => $this->pagination(),
            'postEyebrow' => $this->postEyebrow(),
            'postLede' => $this->postLede(),
            'readingMinutes' => $this->readingMinutes(),
            'adjacentPosts' => $this->adjacentPosts(),
            'relatedPosts' => $this->relatedPosts(),
        ];
    }

    /**
     * Retrieve the post title.
     */
    public function title(): string
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Latest Posts', 'acreline');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Search Results for %s', 'acreline'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Not Found', 'acreline');
        }

        return get_the_title();
    }

    /**
     * Retrieve the pagination links.
     */
    public function pagination(): string
    {
        return wp_link_pages([
            'echo' => 0,
            'before' => '<p>'.__('Pages:', 'acreline'),
            'after' => '</p>',
        ]);
    }

    public function postEyebrow(): string
    {
        if (! is_singular('post')) {
            return '';
        }
        $cats = wp_strip_all_tags(get_the_category_list(' · '));

        return ($cats !== '' ? $cats : 'Notes').' · '.$this->readingMinutes().' min · '.get_the_date();
    }

    public function postLede(): string
    {
        if (has_excerpt()) {
            return wp_strip_all_tags(get_the_excerpt());
        }

        return 'A short note for buyers comparing farms, historic houses, and acreage.';
    }

    public function readingMinutes(): int
    {
        $words = str_word_count(wp_strip_all_tags((string) get_the_content()));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * @return array{prev: array{title: string, url: string}|null, next: array{title: string, url: string}|null}
     */
    public function adjacentPosts(): array
    {
        if (! is_singular('post')) {
            return ['prev' => null, 'next' => null];
        }

        $prev = get_adjacent_post(false, '', true);
        $next = get_adjacent_post(false, '', false);

        return [
            'prev' => $prev instanceof \WP_Post ? [
                'title' => get_the_title($prev),
                'url' => (string) get_permalink($prev),
            ] : null,
            'next' => $next instanceof \WP_Post ? [
                'title' => get_the_title($next),
                'url' => (string) get_permalink($next),
            ] : null,
        ];
    }

    /**
     * @return list<array{id: int, title: string, url: string, excerpt: string, image: string, alt: string, meta: string}>
     */
    public function relatedPosts(): array
    {
        if (! is_singular('post')) {
            return [];
        }

        $posts = get_posts([
            'numberposts' => 2,
            'post__not_in' => [(int) get_the_ID()],
            'post_status' => 'publish',
            'orderby' => 'date',
        ]);

        $out = [];
        foreach ($posts as $post) {
            $id = (int) $post->ID;
            $cats = wp_strip_all_tags(get_the_category_list(' · ', '', $id));
            $out[] = [
                'id' => $id,
                'title' => get_the_title($id),
                'url' => (string) get_permalink($id),
                'excerpt' => get_the_excerpt($id),
                'image' => HeroImage::cardUrl($id),
                'alt' => HeroImage::cardAlt($id),
                'meta' => ($cats !== '' ? $cats : 'Notes').' · '.get_the_date('', $id),
            ];
        }

        return $out;
    }
}
