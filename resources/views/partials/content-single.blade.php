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
      ['href' => home_url('/listings'), 'label' => 'Browse listings', 'class' => 'btn btn-primary'],
      ['href' => home_url('/book/'), 'label' => 'Book a showing', 'class' => 'btn btn-outline light'],
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
              <a href="{{ $adjacentPosts['prev']['url'] }}">← {{ $adjacentPosts['prev']['title'] }}</a>
            @else
              <span></span>
            @endif
            <a href="{{ home_url('/blog') }}">All posts</a>
            @if (! empty($adjacentPosts['next']))
              <a href="{{ $adjacentPosts['next']['url'] }}">{{ $adjacentPosts['next']['title'] }} →</a>
            @else
              <span></span>
            @endif
          </nav>
        @endif
      </div>

      <aside class="post-aside" aria-label="Next steps">
        <div class="listing-agent-card">
          <p class="eyebrow">Next step</p>
          <h2>Walk a sample property</h2>
          <p>This note is for buyers comparing farms, historic houses, and acreage. Use the same tools a working realtor site would put next to the article.</p>
          <a class="btn btn-primary" href="{{ home_url('/book/') }}">Book a showing</a>
          <a class="btn btn-outline" href="{{ home_url('/listings') }}">Browse listings</a>
          <a class="agent-phone" href="{{ home_url('/guide') }}">Buyer tools →</a>
        </div>
        <div class="scan-card">
          <span class="num">In this note</span>
          <h3>{{ $postEyebrow }}</h3>
          <ul>
            <li>{{ $readingMinutes }} min read</li>
            <li>Published {{ get_the_date() }}</li>
            <li>Sample-market concept copy</li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  @if ($relatedPosts)
  <section class="section section-alt" aria-labelledby="related-posts-heading">
    <div class="wrap">
      <div class="section-head left reveal">
        <p class="eyebrow">Keep reading</p>
        <h2 id="related-posts-heading">More notes for buyers</h2>
        <p>Short posts you can adapt for local SEO — showings, checklists, and land vs home search.</p>
      </div>
      <div class="blog-grid reveal">
        @foreach ($relatedPosts as $related)
          <a class="blog-card" href="{{ $related['url'] }}">
            <img src="{{ $related['image'] }}" width="900" height="560" alt="{{ $related['alt'] }}" loading="lazy" decoding="async">
            <div class="blog-card-body">
              <span class="blog-meta">{{ $related['meta'] }}</span>
              <h3>{!! $related['title'] !!}</h3>
              <p>{{ $related['excerpt'] }}</p>
              <span class="teaser-link">Read post →</span>
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
        <h2>Tour a sample home next.</h2>
        <p>Pick an address, choose a slot, and see how a modern realtor booking flow feels.</p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="{{ home_url('/book/') }}">Book a showing</a>
          <a class="btn btn-outline light" href="{{ home_url('/listings') }}">Browse listings</a>
        </div>
      </div>
    </div>
  </section>
</article>
