<?php

namespace App\View\Composers;

use App\Support\Breadcrumbs;
use App\Support\Catalog;
use App\Support\ColorSchemes;
use App\Support\Compliance;
use App\Support\Faqs;
use App\Support\HeroImage;
use App\Support\Identity;
use App\Support\Navigation;
use App\Support\PageCopy;
use Roots\Acorn\View\Composer;

class App extends Composer
{
    /**
     * @var array
     */
    protected static $views = [
        '*',
    ];

    public function siteName(): string
    {
        return get_bloginfo('name', 'display');
    }

    public function with(): array
    {
        $copy = PageCopy::all();
        $heroOverride = is_array($copy) ? ($copy['hero_image'] ?? '') : '';
        $heroContext = PageCopy::schemaKeyForContext();
        $hero = HeroImage::forRequest($heroOverride, $heroContext);

        return [
            'copy' => $copy,
            'hero_image_url' => $hero['url'],
            'hero_image_srcset' => $hero['srcset'],
            'hero_image_alt' => $hero['alt'],
            'catalogListings' => Catalog::listings(),
            'spotlightListings' => Catalog::featuredListings(3),
            'catalogAgents' => Catalog::agents(),
            'catalogTownships' => Catalog::townships(),
            'selectedListingId' => (int) ($_GET['listing_id'] ?? $_GET['listing'] ?? 0),
            'showingTypes' => Catalog::SHOWING_TYPES,
            'areaCards' => PageCopy::areaCards(),
            'faqs' => Faqs::forContext(),
            'identity' => Identity::toArray(),
            'compliance' => Compliance::toArray(),
            'primaryNav' => Navigation::items('primary_navigation'),
            'footerNav' => Navigation::items('footer_navigation'),
            'breadcrumbs' => Breadcrumbs::items(),
            'breadcrumbLd' => Breadcrumbs::jsonLd(),
            'keystone' => [
                'homeUrl' => home_url('/'),
                'listingsUrl' => Navigation::pageUrl('listings'),
                'bookUrl' => Identity::bookUrl(),
                'blogUrl' => Navigation::pageUrl('blog', '/blog'),
                'restUrl' => rest_url('keystone/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'listings' => Catalog::listings(),
                'schemes' => ColorSchemes::forJs(),
                'compliance' => Compliance::forJs(),
            ],
        ];
    }
}
