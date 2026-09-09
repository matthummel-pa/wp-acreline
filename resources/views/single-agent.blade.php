@extends('layouts.app')

@section('content')
@php
  $agent = \App\Support\Catalog::agent((int) get_the_ID());
  $listings = $agent ? \App\Support\Catalog::listingsForAgent($agent['id']) : [];
@endphp
@if ($agent)
@include('partials.breadcrumbs')


@include('partials.page-hero', [
  'heroBrand' => $identity['brand'] ?? 'Acreline',
  'heroEyebrow' => $agent['job_title'],
  'heroTitle' => $agent['name'],
  'heroText' => trim($agent['office'].($agent['years_experience'] ? ' · '.$agent['years_experience'].' '.esc_html__('years', 'acreline') : '')),
  'headingId' => 'agent-hero-heading',
  'heroActions' => [
    ['href' => home_url('/book/'), 'label' => __('Book a showing', 'acreline'), 'class' => 'btn btn-primary'],
    ['href' => home_url('/contact'), 'label' => __('Contact the office', 'acreline'), 'class' => 'btn btn-outline light'],
  ],
])

<section class="section">
  <div class="wrap listing-single">
    <div class="listing-single-main prose">
      <p>{{ $agent['bio'] }}</p>
      <dl class="agent-facts">
        @if ($agent['license_number'])
          <div><dt>{{ __('License', 'acreline') }}</dt><dd>{{ $agent['license_state'] }} {{ $agent['license_number'] }}</dd></div>
        @endif
        @if ($agent['mls_id'])
          <div><dt>{{ __('MLS ID', 'acreline') }}</dt><dd>{{ $agent['mls_id'] }}</dd></div>
        @endif
        @if ($agent['nrds_id'])
          <div><dt>{{ __('NRDS', 'acreline') }}</dt><dd>{{ $agent['nrds_id'] }}</dd></div>
        @endif
        @if ($agent['specialties'])
          <div><dt>{{ __('Specialties', 'acreline') }}</dt><dd>{{ $agent['specialties'] }}</dd></div>
        @endif
        @if ($agent['service_areas'])
          <div><dt>{{ __('Service areas', 'acreline') }}</dt><dd>{{ $agent['service_areas'] }}</dd></div>
        @endif
        @if ($agent['languages'])
          <div><dt>{{ __('Languages', 'acreline') }}</dt><dd>{{ $agent['languages'] }}</dd></div>
        @endif
        @if ($agent['designations'])
          <div><dt>{{ __('Designations', 'acreline') }}</dt><dd>{{ $agent['designations'] }}</dd></div>
        @endif
      </dl>
    </div>
    <aside class="listing-agent-card">
      <p class="eyebrow">{{ __('Contact', 'acreline') }}</p>
      @if ($agent['phone'])
        <a class="agent-phone" href="{{ \App\Support\Catalog::telHref($agent['phone']) }}">{{ $agent['phone'] }}</a>
      @endif
      @if ($agent['email'])
        <a class="agent-phone" href="mailto:{{ $agent['email'] }}">{{ $agent['email'] }}</a>
      @endif
      <a class="btn btn-primary" href="{{ home_url('/book/') }}" style="margin-top:16px">{{ __('Book a showing', 'acreline') }}</a>
    </aside>
  </div>
</section>

@if ($listings)
<section class="section section-alt">
  <div class="wrap">
    <h2>{{ sprintf(__('Listings with %s', 'acreline'), $agent['name']) }}</h2>
    <div class="listing-mini-grid reveal" style="margin-top:24px">
      @foreach ($listings as $listing)
        <a class="listing-mini" href="{{ $listing['permalink'] }}" aria-label="{{ esc_attr($listing['title']) }}">
          @if ($listing['image'])
            <img src="{{ $listing['image'] }}" width="800" height="500" alt="{{ esc_attr($listing['title']) }}" loading="lazy">
          @else
            <div class="listing-mini-photo" role="img" aria-hidden="true" style="background:{{ $listing['grad'] }};height:150px"></div>
          @endif
          <div>
            <strong>{{ \App\Support\Catalog::formatMoney((int) $listing['price']) }}</strong>
            <span>{{ $listing['title'] }}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif
@endif
@endsection
