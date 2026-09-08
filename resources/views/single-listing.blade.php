@extends('layouts.app')

@section('content')
@php
  $listing = \App\Support\Catalog::listing((int) get_the_ID());
  $agent = $listing && $listing['listing_agent'] ? \App\Support\Catalog::agent((int) $listing['listing_agent']) : null;
@endphp
@if ($listing)
  @php
    $listingSchema = [
      '@context' => 'https://schema.org',
      '@type' => $listing['type'] === 'land' ? 'Place' : 'SingleFamilyResidence',
      'name' => html_entity_decode($listing['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'description' => html_entity_decode($listing['desc'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
      'url' => $listing['permalink'],
      'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $listing['address'],
        'addressLocality' => $listing['city'],
        'addressRegion' => $listing['state'] ?: 'PA',
        'postalCode' => $listing['zip'],
        'addressCountry' => 'US',
      ],
      'offers' => [
        '@type' => 'Offer',
        'price' => (int) $listing['price'],
        'priceCurrency' => 'USD',
        'availability' => $listing['status'] === 'sold'
          ? 'https://schema.org/SoldOut'
          : 'https://schema.org/InStock',
      ],
    ];
    if ($listing['image']) {
      $listingSchema['image'] = $listing['image'];
    }
    if ($listing['type'] !== 'land' && $listing['beds']) {
      $listingSchema['numberOfRooms'] = (float) $listing['beds'];
    }
  @endphp
  <script type="application/ld+json">
    {!! json_encode($listingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
@include('partials.breadcrumbs')

@include('partials.page-hero', [
  'heroBrand' => $listing['typeLabel'].' · '.$listing['township'].' Township',
  'heroEyebrow' => strtoupper($listing['status']).($listing['mls_number'] ? ' · MLS '.$listing['mls_number'] : ''),
  'heroTitle' => $listing['title'],
  'heroText' => $listing['address'] ?: $listing['desc'],
  'headingId' => 'listing-hero-heading',
  'heroActions' => [
    ['href' => home_url('/book/').'?listing_id='.$listing['id'], 'label' => 'Book a showing', 'class' => 'btn btn-primary'],
    ['href' => home_url('/listings'), 'label' => 'All listings', 'class' => 'btn btn-outline light'],
  ],
])

<section class="section">
  <div class="wrap listing-single">
    <div class="listing-single-main">
      <div
        class="listing-single-photo"
        role="img"
        aria-label="{{ esc_attr($listing['title']) }}"
        style="@if ($listing['image']) background-image:url({{ $listing['image'] }});background-size:cover;background-position:center;@else background:{{ $listing['grad'] }};@endif"
      ></div>
      <p class="modal-price">{{ \App\Support\Catalog::formatMoney((int) $listing['price']) }}</p>
      <div class="modal-specs">
        @if ($listing['type'] !== 'land')
          <div><strong>{{ $listing['beds'] }}</strong><span>{{ __('Beds', 'acreline') }}</span></div>
          <div><strong>{{ $listing['baths'] }}</strong><span>{{ __('Baths', 'acreline') }}</span></div>
          <div><strong>{{ number_format((int) $listing['sqft']) }}</strong><span>{{ __('Sq Ft', 'acreline') }}</span></div>
        @endif
        <div><strong>{{ $listing['acres'] }}</strong><span>{{ __('Acres', 'acreline') }}</span></div>
        @if ($listing['year_built'])
          <div><strong>{{ $listing['year_built'] }}</strong><span>{{ __('Year built', 'acreline') }}</span></div>
        @endif
      </div>
      <div class="prose">
        <p>{{ $listing['desc'] }}</p>
      </div>
      <div class="cta-actions" style="margin-top:24px">
        <a class="btn btn-primary" href="{{ home_url('/book/') }}?listing_id={{ $listing['id'] }}">{{ __('Book a showing', 'acreline') }}</a>
        <a class="btn btn-outline" href="{{ home_url('/listings') }}">{{ __('All listings', 'acreline') }}</a>
      </div>
    </div>
    @if ($agent)
      <aside class="listing-agent-card">
        <p class="eyebrow">{{ __('Listing agent', 'acreline') }}</p>
        <h2><a href="{{ $agent['permalink'] }}">{{ $agent['name'] }}</a></h2>
        <p class="agent-title">{{ $agent['job_title'] }}</p>
        <p>{{ $agent['bio'] }}</p>
        @if ($agent['phone'])
          <a class="agent-phone" href="{{ \App\Support\Catalog::telHref($agent['phone']) }}">{{ $agent['phone'] }}</a>
        @endif
      </aside>
    @endif
  </div>
</section>
@endif
@endsection
