<article @php(post_class('h-entry post-article'))>
  @include('partials.breadcrumbs')

  @include('partials.page-hero', [
    'heroBrand' => '',
    'heroEyebrow' => $postEyebrow,
    'heroTitle' => $title,
    'heroText' => $postLede,
    'headingId' => 'post-hero-heading',
    'heroClass' => 'page-hero--article',
    'heroActions' => [
      ['href' => home_url('/listings'), 'label' => __('Browse listings', 'acreline'), 'class' => 'btn btn-primary'],
      ['href' => home_url('/book/'), 'label' => __('Book a showing', 'acreline'), 'class' => 'btn btn-outline light'],
    ],
  ])

  <section class="section">
    <div class="wrap post-layout">
      <div class="post-main">
        <div class="e-content post-body">
          @php(the_content())
        </div>

        @if ($pagination)
          <nav class="page-nav" aria-label="Page">
            {!! $pagination !!}
          </nav>
        @endif

        @if ($adjacentPosts)
          <nav class="post-nav" aria-label="More posts">
            @if (! empty($adjacentPosts['prev']))
              <a href="{{ $adjacentPosts['prev']['url'] }}" rel="prev">← {{ $adjacentPosts['prev']['title'] }}</a>
            @else
              <span></span>
            @endif
            <a href="{{ home_url('/blog') }}">{{ __('All posts', 'acreline') }}</a>
            @if (! empty($adjacentPosts['next']))
              <a href="{{ $adjacentPosts['next']['url'] }}" rel="next">{{ $adjacentPosts['next']['title'] }} →</a>
            @else
              <span></span>
            @endif
          </nav>
        @endif
      </div>

      <aside class="post-aside" aria-label="{{ esc_attr__('Next steps', 'acreline') }}">
        <div class="listing-agent-card">
          <p class="eyebrow">{{ __('Next step', 'acreline') }}</p>
          <h2>{{ __('Walk a sample property', 'acreline') }}</h2>
          <p>{{ __('This note is for buyers comparing homes, neighborhoods, and showing logistics. Use the same tools a working agent site would put next to the article.', 'acreline') }}</p>
          <a class="btn btn-primary" href="{{ home_url('/book/') }}">{{ __('Book a showing', 'acreline') }}</a>
          <a class="btn btn-outline" href="{{ home_url('/listings') }}">{{ __('Browse listings', 'acreline') }}</a>
          <a class="agent-phone" href="{{ home_url('/guide') }}">{{ __('Buyer tools →', 'acreline') }}</a>
        </div>
        <div class="scan-card">
          <span class="num">{{ __('In this note', 'acreline') }}</span>
          <h3>{{ $postEyebrow }}</h3>
          <ul>
            <li>{{ sprintf(__('%d min read', 'acreline'), $readingMinutes) }}</li>
            <li>{{ sprintf(__('Published %s', 'acreline'), get_the_date()) }}</li>
            <li>{{ __('Sample-market concept copy', 'acreline') }}</li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  @if ($relatedPosts)
  <section class="section section-alt" aria-labelledby="related-posts-heading">
    <div class="wrap">
      <div class="section-head left reveal">
        <p class="eyebrow">{{ __('Keep reading', 'acreline') }}</p>
        <h2 id="related-posts-heading">{{ __('More notes for buyers', 'acreline') }}</h2>
        <p>{{ __('Short posts you can adapt for local SEO — showings, checklists, and land vs home search.', 'acreline') }}</p>
      </div>
      <div class="blog-grid reveal">
        @foreach ($relatedPosts as $related)
          <a class="blog-card" href="{{ $related['url'] }}">
            <img src="{{ $related['image'] }}" width="900" height="560" alt="{{ $related['alt'] }}" loading="lazy" decoding="async">
            <div class="blog-card-body">
              <span class="blog-meta">{{ $related['meta'] }}</span>
              <h3>{!! $related['title'] !!}</h3>
              <p>{{ $related['excerpt'] }}</p>
              <span class="teaser-link">{{ __('Read post →', 'acreline') }}</span>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <section class="section">
    <div class="wrap">
      <div class="cta-band reveal">
        <h2>{{ __('Tour a sample home next.', 'acreline') }}</h2>
        <p>{{ __('Pick an address, choose a slot, and see how a modern realtor booking flow feels.', 'acreline') }}</p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="{{ home_url('/book/') }}">{{ __('Book a showing', 'acreline') }}</a>
          <a class="btn btn-outline light" href="{{ home_url('/listings') }}">{{ __('Browse listings', 'acreline') }}</a>
        </div>
      </div>
    </div>
  </section>
</article>
