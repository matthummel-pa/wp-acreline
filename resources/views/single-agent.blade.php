@extends('layouts.app')

@section('content')
@php
  $agent = \App\Support\Catalog::agent((int) get_the_ID());
  $listings = $agent ? \App\Support\Catalog::listingsForAgent($agent['id']) : [];

  $showStats    = \App\ks_setting('agent_show_stats') !== '0';
  $showSocial   = \App\ks_setting('agent_show_social') !== '0';
  $showCerts    = \App\ks_setting('agent_show_certifications') !== '0';
  $showAwards   = \App\ks_setting('agent_show_awards') !== '0';
  $showVideo    = \App\ks_setting('agent_show_bio_video') !== '0';
  $showTeam     = \App\ks_setting('agent_show_team') !== '0';
  $showCalendly = \App\ks_setting('agent_show_calendly') !== '0';
@endphp
@if ($agent)
@include('partials.breadcrumbs')

@include('partials.page-hero', [
  'heroBrand' => $identity['brand'] ?? 'Acreline',
  'heroEyebrow' => $agent['job_title'].($showTeam && $agent['team_name'] ? ' · '.$agent['team_name'] : ''),
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

      {{-- ── Photo & badge row ──────────────────────────────────────────────── --}}
      @if ($agent['photo'] || $agent['featured_badge'])
      <div class="agent-profile-row">
        @if ($agent['photo'])
          <img class="agent-profile-photo" src="{{ $agent['photo'] }}" width="140" height="140" alt="{{ esc_attr($agent['name']) }}" loading="lazy">
        @endif
        <div class="agent-profile-meta">
          @if ($agent['featured_badge'])
            <span class="agent-badge">{{ $agent['featured_badge'] }}</span>
          @endif
        </div>
      </div>
      @endif

      {{-- ── Bio ────────────────────────────────────────────────────────────── --}}
      <p>{{ $agent['bio'] }}</p>

      {{-- ── Intro video ─────────────────────────────────────────────────────── --}}
      @if ($showVideo && $agent['bio_video'])
      <p><a class="btn btn-outline btn-sm" href="{{ $agent['bio_video'] }}" target="_blank" rel="noopener">&#x25B6; {{ __('Watch intro video', 'acreline') }}</a></p>
      @endif

      {{-- ── Performance stats ───────────────────────────────────────────────── --}}
      @if ($showStats)
      @php
        $stats = array_filter([
          __('Homes sold', 'acreline') => $agent['homes_sold'],
          __('Avg. days on market', 'acreline') => $agent['avg_dom'],
          __('List-to-sale ratio', 'acreline') => $agent['list_to_sale_ratio'] ? $agent['list_to_sale_ratio'].'%' : '',
          __('Closed volume', 'acreline') => $agent['total_volume'],
          __('Client reviews', 'acreline') => $agent['client_reviews_count'],
        ]);
      @endphp
      @if (!empty($stats))
      <div class="agent-stats-grid">
        @foreach ($stats as $statLabel => $statValue)
          <div class="agent-stat">
            <strong>{{ $statValue }}</strong>
            <span>{{ $statLabel }}</span>
          </div>
        @endforeach
      </div>
      @endif
      @endif

      {{-- ── Credentials & facts ─────────────────────────────────────────────── --}}
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
        @if ($showCerts && $agent['designations'])
          <div><dt>{{ __('Designations', 'acreline') }}</dt><dd>{{ $agent['designations'] }}</dd></div>
        @endif
        @if ($showCerts && $agent['certifications'])
          <div><dt>{{ __('Certifications', 'acreline') }}</dt><dd>{{ $agent['certifications'] }}</dd></div>
        @endif
        @if ($showAwards && $agent['awards'])
          <div><dt>{{ __('Awards', 'acreline') }}</dt><dd>{{ $agent['awards'] }}</dd></div>
        @endif
      </dl>

      {{-- ── Social media ─────────────────────────────────────────────────────── --}}
      @if ($showSocial)
      @php
        $socials = array_filter([
          'Facebook' => $agent['facebook'],
          'Instagram' => $agent['instagram'],
          'LinkedIn' => $agent['linkedin'],
          'X / Twitter' => $agent['twitter'],
          'YouTube' => $agent['youtube'],
        ]);
      @endphp
      @if (!empty($socials))
      <div class="agent-social-row">
        @foreach ($socials as $platform => $url)
          <a class="agent-social-link" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $platform }}</a>
        @endforeach
      </div>
      @endif
      @endif

    </div>

    {{-- ── Sidebar: contact ────────────────────────────────────────────────── --}}
    <aside class="listing-agent-card">
      <p class="eyebrow">{{ __('Contact', 'acreline') }}</p>
      @if ($agent['phone'])
        <a class="agent-phone" href="{{ \App\Support\Catalog::telHref($agent['phone']) }}">{{ $agent['phone'] }}</a>
      @endif
      @if ($agent['mobile'] && $agent['mobile'] !== $agent['phone'])
        <a class="agent-phone" href="{{ \App\Support\Catalog::telHref($agent['mobile']) }}">{{ $agent['mobile'] }} ({{ __('mobile', 'acreline') }})</a>
      @endif
      @if ($agent['email'])
        <a class="agent-phone" href="mailto:{{ $agent['email'] }}">{{ $agent['email'] }}</a>
      @endif
      @if ($agent['website'])
        <a class="agent-website-link" href="{{ $agent['website'] }}" target="_blank" rel="noopener">{{ __('Personal website', 'acreline') }}</a>
      @endif
      <a class="btn btn-primary" href="{{ home_url('/book/') }}" style="margin-top:16px">{{ __('Book a showing', 'acreline') }}</a>
      @if ($showCalendly && $agent['calendly'])
        <a class="btn btn-outline" href="{{ $agent['calendly'] }}" target="_blank" rel="noopener" style="margin-top:8px">{{ __('Schedule a call', 'acreline') }}</a>
      @endif
    </aside>
  </div>
</section>

<section class="section section-alt" aria-labelledby="agent-help-heading">
  <div class="wrap">
    <header class="section-head left reveal">
      <p class="eyebrow">How this walk works</p>
      <h2 id="agent-help-heading">What {{ $agent['name'] }} is for</h2>
      <p>Rural Adams County showings are not a 20-minute condo tour. Scan this, then book a sample slot or call the concept line.</p>
    </header>
    <div class="scan-grid cols-3 reveal">
      <article class="scan-card">
        <span class="num">Specialty</span>
        <h3>{{ $agent['specialties'] ?: 'Farms, houses, and land' }}</h3>
        <ul>
          <li>{{ $agent['service_areas'] ?: 'Adams County townships and nearby boroughs' }}</li>
          <li>Boots, well questions, and a recorded lane</li>
          <li>Honest notes on wet corners and rollback risk</li>
        </ul>
      </article>
      <article class="scan-card">
        <span class="num">On the walk</span>
        <h3>What gets checked</h3>
        <ul>
          <li>Access, well, and septic feasibility</li>
          <li>Zoning and easements before an offer</li>
          <li>Barn, shop, or historic systems if they apply</li>
        </ul>
      </article>
      <article class="scan-card">
        <span class="num">Next</span>
        <h3>Stay moving</h3>
        <ul>
          <li><a href="{{ home_url('/book/') }}">Book a showing</a> with a sample address</li>
          <li>Read the <a href="{{ home_url('/guide') }}">buyer guide</a> for perc and Act 319</li>
          <li>Browse <a href="{{ home_url('/listings') }}">listings</a> assigned below</li>
        </ul>
      </article>
    </div>
  </div>
</section>

@include('partials.faq-list', [
  'faqTitle' => 'Agent questions',
  'faqText' => 'How to reach this sample profile — and what is fictional.',
  'faqHeadClass' => 'left',
  'faqSectionClass' => '',
])

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
