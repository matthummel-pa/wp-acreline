@extends('layouts.app')

@section('content')
  @include('partials.breadcrumbs')

  @include('partials.page-hero', [
    'heroBrand' => $identity['brand'] ?? 'Acreline',
    'heroEyebrow' => '404',
    'heroTitle' => 'That page isn’t here.',
    'heroText' => 'The link may be old, or this demo page hasn’t been created yet. Use the paths below — they match the concept site.',
    'headingId' => 'error-hero-heading',
    'heroActions' => [
      ['href' => home_url('/'), 'label' => 'Back home', 'class' => 'btn btn-primary'],
      ['href' => home_url('/listings'), 'label' => 'Browse listings', 'class' => 'btn btn-outline light'],
    ],
  ])

  <section class="section">
    <div class="wrap">
      <div class="intent-grid dest-grid">
        <a class="intent-card" href="{{ home_url('/') }}">
          <strong>Home</strong>
          <span>Search samples, price a demo home, or book a showing.</span>
          <em>Go to homepage →</em>
        </a>
        <a class="intent-card" href="{{ home_url('/listings') }}">
          <strong>Listings</strong>
          <span>Eight fictional homes, farms, and land parcels.</span>
          <em>Browse inventory →</em>
        </a>
        <a class="intent-card" href="{{ home_url('/book/') }}">
          <strong>Book</strong>
          <span>Pick a sample address, date, and time slot.</span>
          <em>Request a showing →</em>
        </a>
        <a class="intent-card" href="{{ home_url('/guide') }}">
          <strong>Buyer guide</strong>
          <span>Wells, perc, access, and demo land-loan math.</span>
          <em>Open buyer tools →</em>
        </a>
        <a class="intent-card" href="{{ home_url('/areas') }}">
          <strong>Areas</strong>
          <span>Township-by-township reads of rural Adams County.</span>
          <em>Scan townships →</em>
        </a>
        <a class="intent-card" href="{{ home_url('/contact') }}">
          <strong>Contact</strong>
          <span>100 Concept Way · (555) 010-0455 · sample form.</span>
          <em>Reach the office →</em>
        </a>
      </div>
    </div>
  </section>
@endsection
