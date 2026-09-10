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

  // Pre-split arrays for pill display
  $specialtyList = $agent ? array_filter(array_map('trim', explode(',', $agent['specialties']))) : [];
  $areaList      = $agent ? array_filter(array_map('trim', explode(',', $agent['service_areas']))) : [];
  $langList      = $agent ? array_filter(array_map('trim', explode(',', $agent['languages']))) : [];
  $certList      = $agent ? array_filter(array_map('trim', explode('·', $agent['certifications']))) : [];
  $awardList     = $agent ? array_filter(array_map('trim', explode('·', $agent['awards']))) : [];
  $desgList      = $agent ? array_filter(array_map('trim', explode(',', $agent['designations']))) : [];

  // Star rendering helper
  $starRating    = $agent ? (float) $agent['rating'] : 0;
  $fullStars     = (int) floor($starRating);
  $halfStar      = ($starRating - $fullStars) >= 0.5;
@endphp

@if ($agent)
@include('partials.breadcrumbs')

{{-- ── Agent hero banner ──────────────────────────────────────────────────── --}}
<section class="ap-hero" aria-labelledby="agent-hero-heading">
  <div class="wrap ap-hero__inner">
    {{-- Portrait --}}
    <div class="ap-hero__portrait">
      @if ($agent['photo'])
        <img
          class="ap-hero__photo"
          src="{{ $agent['photo'] }}"
          width="200" height="200"
          alt="{{ esc_attr($agent['name']) }}"
          loading="eager"
        >
      @else
        <div class="ap-hero__avatar" style="background:{{ $agent['avatar_color'] }}" aria-hidden="true">
          <span>{{ $agent['initials'] }}</span>
        </div>
      @endif
      @if ($agent['featured'])
        <span class="ap-hero__badge">{{ $agent['featured_badge'] ?: __('Featured', 'acreline') }}</span>
      @endif
    </div>

    {{-- Identification --}}
    <div class="ap-hero__meta">
      @if ($agent['transaction_types'])
        <p class="ap-hero__type-line">{{ $agent['transaction_types'] }}</p>
      @endif
      <h1 id="agent-hero-heading" class="ap-hero__name">{{ $agent['name'] }}</h1>
      <p class="ap-hero__title">
        {{ $agent['job_title'] }}
        @if ($showTeam && $agent['team_name'])
          <span class="ap-hero__team"> · {{ $agent['team_name'] }}</span>
        @endif
      </p>
      @if ($agent['tag_line'])
        <p class="ap-hero__tagline">"{{ $agent['tag_line'] }}"</p>
      @endif

      {{-- Star rating inline --}}
      @if ($starRating > 0)
      <div class="ap-hero__stars" aria-label="{{ sprintf(__('Rated %s out of 5', 'acreline'), $agent['rating']) }}">
        @for ($i = 1; $i <= 5; $i++)
          @if ($i <= $fullStars)
            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.76.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.27 6.62l5.34-.78z"/></svg>
          @elseif ($halfStar && $i === $fullStars + 1)
            <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><defs><linearGradient id="hg{{ $agent['id'] }}"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="var(--line)"/></linearGradient></defs><path fill="url(#hg{{ $agent['id'] }})" d="M10 1l2.39 4.84 5.34.78-3.86 3.76.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.27 6.62l5.34-.78z"/></svg>
          @else
            <svg width="18" height="18" viewBox="0 0 20 20" fill="var(--line)" aria-hidden="true"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.76.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.27 6.62l5.34-.78z"/></svg>
          @endif
        @endfor
        <strong class="ap-hero__rating-num">{{ $agent['rating'] }}</strong>
        @if ($agent['client_reviews_count'])
          <span class="ap-hero__review-count">({{ $agent['client_reviews_count'] }} {{ __('reviews', 'acreline') }})</span>
        @endif
      </div>
      @endif

      {{-- CTA row --}}
      <div class="ap-hero__actions">
        <a class="btn btn-primary" href="{{ home_url('/book/') }}">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="14" height="13" rx="2"/><path d="M7 2v4M13 2v4M3 9h14"/></svg>
          {{ __('Book a showing', 'acreline') }}
        </a>
        @if ($agent['phone'])
          <a class="btn btn-outline" href="{{ \App\Support\Catalog::telHref($agent['phone']) }}">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 5a2 2 0 012-2h.6a1 1 0 01.95.68l.82 2.46a1 1 0 01-.23 1.02L5.6 8.7c1.07 2.1 2.6 3.63 4.7 4.7l1.54-1.54a1 1 0 011.02-.23l2.46.82A1 1 0 0117 13.4V14a2 2 0 01-2 2h-.5C7.6 16 4 12.4 4 8V5z"/></svg>
            {{ $agent['phone'] }}
          </a>
        @endif
        @if ($showCalendly && $agent['calendly'])
          <a class="btn btn-outline" href="{{ $agent['calendly'] }}" target="_blank" rel="noopener">
            {{ __('Schedule a call', 'acreline') }}
          </a>
        @endif
      </div>

      {{-- Office & experience pills --}}
      <div class="ap-hero__meta-pills">
        @if ($agent['office'])
          <span class="ap-meta-pill">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 19V7l6-5 6 5v12"/><path d="M9 19v-6h2v6"/></svg>
            {{ $agent['office'] }}
          </span>
        @endif
        @if ($agent['years_experience'])
          <span class="ap-meta-pill">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l3 3"/></svg>
            {{ $agent['years_experience'] }} {{ __('yrs experience', 'acreline') }}
          </span>
        @endif
        @if ($agent['license_number'])
          <span class="ap-meta-pill">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="14" height="14" rx="2"/><path d="M7 10h6M7 13h4"/></svg>
            {{ $agent['license_state'] }} Lic. {{ $agent['license_number'] }}
          </span>
        @endif
        @foreach ($langList as $lang)
          <span class="ap-meta-pill">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M2 10h16M10 2a14 14 0 010 16"/></svg>
            {{ trim($lang) }}
          </span>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ── Performance stats strip ────────────────────────────────────────────── --}}
@if ($showStats)
@php
  $statItems = array_filter([
    ['val' => $agent['homes_sold'],       'lbl' => __('Homes closed', 'acreline'),      'icon' => 'home'],
    ['val' => $agent['avg_dom']           ? $agent['avg_dom'].' days'         : '', 'lbl' => __('Avg. days on market', 'acreline'),  'icon' => 'clock'],
    ['val' => $agent['list_to_sale_ratio'] ? $agent['list_to_sale_ratio'].'%' : '', 'lbl' => __('List-to-sale ratio', 'acreline'),   'icon' => 'chart'],
    ['val' => $agent['total_volume']      ? '$'.number_format((float) $agent['total_volume'] / 1000000, 1).'M' : '', 'lbl' => __('Closed volume', 'acreline'), 'icon' => 'dollar'],
    ['val' => $agent['client_reviews_count'], 'lbl' => __('Client reviews', 'acreline'), 'icon' => 'star'],
  ], fn($s) => ! empty($s['val']));
@endphp
@if (!empty($statItems))
<div class="ap-stats-strip" role="list" aria-label="{{ __('Agent performance statistics', 'acreline') }}">
  @foreach ($statItems as $stat)
    <div class="ap-stats-strip__item" role="listitem">
      @if ($stat['icon'] === 'home')
        <svg class="ap-stats-strip__icon" width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 9.5L10 3l7 6.5"/><path d="M5 9v7h4v-4h2v4h4V9"/></svg>
      @elseif ($stat['icon'] === 'clock')
        <svg class="ap-stats-strip__icon" width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l3 3"/></svg>
      @elseif ($stat['icon'] === 'chart')
        <svg class="ap-stats-strip__icon" width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><polyline points="2 14 7 9 11 12 18 5"/></svg>
      @elseif ($stat['icon'] === 'dollar')
        <svg class="ap-stats-strip__icon" width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><line x1="10" y1="2" x2="10" y2="18"/><path d="M14 6H8a2 2 0 000 4h4a2 2 0 010 4H6"/></svg>
      @else
        <svg class="ap-stats-strip__icon" width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
      @endif
      <strong class="ap-stats-strip__val">{{ $stat['val'] }}</strong>
      <span class="ap-stats-strip__lbl">{{ $stat['lbl'] }}</span>
    </div>
  @endforeach
</div>
@endif
@endif

{{-- ── Main profile layout ─────────────────────────────────────────────────── --}}
<section class="section">
  <div class="wrap ap-profile-grid">

    {{-- ── LEFT / MAIN COLUMN ─────────────────────────────────────────────── --}}
    <div class="ap-main">

      {{-- Bio --}}
      <div class="ap-section prose">
        <h2 class="ap-section__heading">{{ __('About', 'acreline') }} {{ $agent['name'] }}</h2>
        <p>{{ $agent['bio'] }}</p>
      </div>

      {{-- Bio video --}}
      @if ($showVideo && $agent['bio_video'])
      <div class="ap-section">
        <a class="ap-video-cta" href="{{ $agent['bio_video'] }}" target="_blank" rel="noopener">
          <span class="ap-video-cta__play" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 20 20" fill="currentColor"><path d="M5 4l12 6-12 6z"/></svg>
          </span>
          <span>
            <strong>{{ __('Watch intro video', 'acreline') }}</strong>
            <span>{{ __('A quick look at how', 'acreline') }} {{ $agent['name'] }} {{ __('works', 'acreline') }}</span>
          </span>
          <svg class="ap-video-cta__ext" width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 3h3v3M10 10l7-7M4 5h7M4 15h12"/></svg>
        </a>
      </div>
      @endif

      {{-- How I work --}}
      @if ($agent['process_note'])
      <div class="ap-section ap-process">
        <h2 class="ap-section__heading">{{ __('How', 'acreline') }} {{ $agent['name'] }} {{ __('works', 'acreline') }}</h2>
        <div class="ap-process__steps">
          <div class="ap-process__step">
            <span class="ap-process__num" aria-hidden="true">01</span>
            <div>
              <strong>{{ __('Walk the property', 'acreline') }}</strong>
              <p>{{ __('On-site before any offer, checking access, systems, and anything that could affect value or livability.', 'acreline') }}</p>
            </div>
          </div>
          <div class="ap-process__step">
            <span class="ap-process__num" aria-hidden="true">02</span>
            <div>
              <strong>{{ __('Understand the issues', 'acreline') }}</strong>
              <p>{{ $agent['process_note'] }}</p>
            </div>
          </div>
          <div class="ap-process__step">
            <span class="ap-process__num" aria-hidden="true">03</span>
            <div>
              <strong>{{ __('Negotiate and close', 'acreline') }}</strong>
              <p>{{ __('Straight-talk pricing, coordinated inspections, and a smooth closing — no surprises at the table.', 'acreline') }}</p>
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Featured review --}}
      @if ($agent['review_snippet'])
      <figure class="ap-review-feature">
        <svg class="ap-review-feature__mark" width="40" height="40" viewBox="0 0 40 40" fill="currentColor" aria-hidden="true"><path d="M11 28c-3.3 0-5-2.3-5-5.5 0-6 4.7-11.8 11-14.5l1.5 2.8C14.2 12.8 12 16 12 19.5V20h3v8H11zm16 0c-3.3 0-5-2.3-5-5.5 0-6 4.7-11.8 11-14.5l1.5 2.8C30.2 12.8 28 16 28 19.5V20h3v8H27z"/></svg>
        <blockquote class="ap-review-feature__quote">
          {{ $agent['review_snippet'] }}
        </blockquote>
        <figcaption class="ap-review-feature__author">
          @if ($starRating > 0)
            <span class="ap-review-feature__stars" aria-hidden="true">
              @for ($i = 1; $i <= 5; $i++)
                <svg width="14" height="14" viewBox="0 0 20 20" fill="{{ $i <= $fullStars ? 'currentColor' : 'var(--line)' }}" aria-hidden="true"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.76.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.27 6.62l5.34-.78z"/></svg>
              @endfor
            </span>
          @endif
          <strong>{{ $agent['review_author'] }}</strong>
          @if ($agent['review_location'])
            <span>&nbsp;· {{ $agent['review_location'] }}</span>
          @endif
        </figcaption>
      </figure>
      @endif

      {{-- Specialties --}}
      @if (!empty($specialtyList))
      <div class="ap-section">
        <h2 class="ap-section__heading">{{ __('Specialties', 'acreline') }}</h2>
        <ul class="ap-pill-list" role="list">
          @foreach ($specialtyList as $sp)
            <li>{{ $sp }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Service areas --}}
      @if (!empty($areaList))
      <div class="ap-section">
        <h2 class="ap-section__heading">{{ __('Service areas', 'acreline') }}</h2>
        <ul class="ap-pill-list ap-pill-list--area" role="list">
          @foreach ($areaList as $area)
            <li>
              <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a6 6 0 016 6c0 4-6 10-6 10S4 12 4 8a6 6 0 016-6zm0 4a2 2 0 100 4 2 2 0 000-4z"/></svg>
              {{ $area }}
            </li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Designations & certifications --}}
      @if ($showCerts && (!empty($desgList) || !empty($certList)))
      <div class="ap-section">
        <h2 class="ap-section__heading">{{ __('Credentials', 'acreline') }}</h2>
        @if (!empty($desgList))
        <div class="ap-cred-block">
          <p class="ap-cred-block__label">{{ __('Designations', 'acreline') }}</p>
          <ul class="ap-pill-list ap-pill-list--cred" role="list">
            @foreach ($desgList as $d)
              <li>
                <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
                {{ $d }}
              </li>
            @endforeach
          </ul>
        </div>
        @endif
        @if (!empty($certList))
        <div class="ap-cred-block">
          <p class="ap-cred-block__label">{{ __('Certifications', 'acreline') }}</p>
          <ul class="ap-cert-list" role="list">
            @foreach ($certList as $cert)
              <li>
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><polyline points="7 10 9 12 13 8"/></svg>
                {{ $cert }}
              </li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
      @endif

      {{-- Awards --}}
      @if ($showAwards && !empty($awardList))
      <div class="ap-section">
        <h2 class="ap-section__heading">{{ __('Recognition', 'acreline') }}</h2>
        <ul class="ap-award-list" role="list">
          @foreach ($awardList as $award)
            <li>
              <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
              {{ $award }}
            </li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- MLS / license detail row --}}
      <div class="ap-section">
        <dl class="ap-id-grid">
          @if ($agent['license_number'])
            <div>
              <dt>{{ __('License', 'acreline') }}</dt>
              <dd>{{ $agent['license_state'] }} {{ $agent['license_number'] }}</dd>
            </div>
          @endif
          @php
            $brokerageAffil = trim((string) ($agent['office'] ?? ''));
            if ($brokerageAffil === '') {
              $brokerageAffil = (string) ($compliance['brokerageLegalName'] ?? '');
            }
          @endphp
          @if ($brokerageAffil !== '')
            <div>
              <dt>{{ __('Brokerage', 'acreline') }}</dt>
              <dd>{{ $brokerageAffil }}</dd>
            </div>
          @endif
          @if ($agent['mls_id'])
            <div>
              <dt>{{ __('MLS ID', 'acreline') }}</dt>
              <dd>{{ $agent['mls_id'] }}</dd>
            </div>
          @endif
          @if ($agent['nrds_id'])
            <div>
              <dt>{{ __('NRDS', 'acreline') }}</dt>
              <dd>{{ $agent['nrds_id'] }}</dd>
            </div>
          @endif
          @if ($agent['office'])
            <div>
              <dt>{{ __('Brokerage', 'acreline') }}</dt>
              <dd>{{ $agent['office'] }}</dd>
            </div>
          @endif
          @if ($agent['years_experience'])
            <div>
              <dt>{{ __('Experience', 'acreline') }}</dt>
              <dd>{{ $agent['years_experience'] }} {{ __('years', 'acreline') }}</dd>
            </div>
          @endif
        </dl>
      </div>

      {{-- Social media --}}
      @if ($showSocial)
      @php
        $socials = array_filter([
          'facebook'  => $agent['facebook'],
          'instagram' => $agent['instagram'],
          'linkedin'  => $agent['linkedin'],
          'twitter'   => $agent['twitter'],
          'youtube'   => $agent['youtube'],
          'website'   => $agent['website'],
        ]);
      @endphp
      @if (!empty($socials))
      <div class="ap-section">
        <h2 class="ap-section__heading sr-only">{{ __('Connect on social media', 'acreline') }}</h2>
        <div class="ap-social-row" role="list">
          @foreach ($socials as $platform => $url)
          <a class="ap-social-link" href="{{ $url }}" target="_blank" rel="noopener noreferrer"
             role="listitem"
             aria-label="{{ ucfirst($platform) }}"
             title="{{ ucfirst($platform) }}">
            @if ($platform === 'facebook')
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12A10 10 0 102 12a10 10 0 0020 0zm-11 5v-4H9v-3h2V8.5A3.5 3.5 0 0114.5 5H16v3h-1.5a1 1 0 00-1 1v1H16l-.5 3h-2.5v4H11z"/></svg>
            @elseif ($platform === 'instagram')
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            @elseif ($platform === 'linkedin')
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.95v5.66H9.35V9h3.42v1.56h.05c.47-.9 1.63-1.85 3.35-1.85 3.59 0 4.25 2.36 4.25 5.43v6.31zM5.34 7.43a2.07 2.07 0 11.01-4.14 2.07 2.07 0 01-.01 4.14zM7.12 20.45H3.56V9H7.12v11.45z"/></svg>
            @elseif ($platform === 'twitter')
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.24 2h3.08L14.17 10l8.43 11.12H13.42L8.36 14.2 2.56 21.12H-.52l7.56-8.65L-.77 2H8.1l4.58 6.06L18.24 2zm-1.08 17.12h1.71L7 3.32H5.17l12 15.8z"/></svg>
            @elseif ($platform === 'youtube')
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.8 8s-.2-1.4-.8-2c-.77-.8-1.63-.8-2.02-.85C16.47 5 12 5 12 5s-4.47 0-6.98.15c-.4.05-1.25.05-2.02.85C2.4 6.6 2.2 8 2.2 8S2 9.63 2 11.25v1.5C2 14.38 2.2 16 2.2 16s.2 1.4.8 2c.77.8 1.78.77 2.23.85C6.72 19 12 19 12 19s4.47 0 6.98-.15c.4-.05 1.25-.05 2.02-.85.6-.6.8-2 .8-2S22 14.38 22 12.75v-1.5C22 9.63 21.8 8 21.8 8zM9.75 14.25v-5l5.5 2.5-5.5 2.5z"/></svg>
            @else
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 000 20"/></svg>
            @endif
          </a>
          @endforeach
        </div>
      </div>
      @endif
      @endif

    </div>{{-- /.ap-main --}}

    {{-- ── RIGHT / SIDEBAR ──────────────────────────────────────────────────── --}}
    <aside class="ap-sidebar" aria-label="{{ __('Contact and booking', 'acreline') }}">

      {{-- Sticky contact card --}}
      <div class="ap-contact-card">
        @if ($agent['photo'])
          <img class="ap-contact-card__photo" src="{{ $agent['photo'] }}" width="72" height="72" alt="{{ esc_attr($agent['name']) }}" loading="lazy">
        @else
          <div class="ap-contact-card__avatar" style="background:{{ $agent['avatar_color'] }}" aria-hidden="true">{{ $agent['initials'] }}</div>
        @endif
        <div class="ap-contact-card__id">
          <strong>{{ $agent['name'] }}</strong>
          <span>{{ $agent['job_title'] }}</span>
        </div>
        @if ($starRating > 0)
          <div class="ap-contact-card__stars" aria-label="{{ sprintf(__('Rated %s', 'acreline'), $agent['rating']) }}">
            @for ($i = 1; $i <= 5; $i++)
              <svg width="13" height="13" viewBox="0 0 20 20" fill="{{ $i <= $fullStars ? 'currentColor' : 'var(--line)' }}" aria-hidden="true"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.76.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.27 6.62l5.34-.78z"/></svg>
            @endfor
            <span>{{ $agent['rating'] }}</span>
          </div>
        @endif

        {{-- Contact rows --}}
        <ul class="ap-contact-list">
          @if ($agent['phone'])
            <li>
              <a href="{{ \App\Support\Catalog::telHref($agent['phone']) }}" class="ap-contact-item">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 5a2 2 0 012-2h.6a1 1 0 01.95.68l.82 2.46a1 1 0 01-.23 1.02L5.6 8.7c1.07 2.1 2.6 3.63 4.7 4.7l1.54-1.54a1 1 0 011.02-.23l2.46.82A1 1 0 0117 13.4V14a2 2 0 01-2 2h-.5C7.6 16 4 12.4 4 8V5z"/></svg>
                {{ $agent['phone'] }}
              </a>
            </li>
          @endif
          @if ($agent['email'])
            <li>
              <a href="mailto:{{ $agent['email'] }}" class="ap-contact-item">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="5" width="16" height="11" rx="2"/><path d="M2 7l8 5 8-5"/></svg>
                {{ $agent['email'] }}
              </a>
            </li>
          @endif
          @if ($agent['website'])
            <li>
              <a href="{{ $agent['website'] }}" target="_blank" rel="noopener" class="ap-contact-item">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M2 10h16M10 2a14 14 0 010 16"/></svg>
                {{ __('Personal website', 'acreline') }}
              </a>
            </li>
          @endif
        </ul>

        <a class="btn btn-primary" href="{{ home_url('/book/') }}" style="width:100%">{{ __('Book a showing', 'acreline') }}</a>
        @if ($showCalendly && $agent['calendly'])
          <a class="btn btn-outline" href="{{ $agent['calendly'] }}" target="_blank" rel="noopener" style="width:100%;margin-top:8px">{{ __('Schedule a call', 'acreline') }}</a>
        @endif
        @if ($agent['office_phone'] && $agent['office_phone'] !== $agent['phone'])
          <p class="ap-contact-card__office-line">
            {{ __('Office', 'acreline') }}: <a href="{{ \App\Support\Catalog::telHref($agent['office_phone']) }}">{{ $agent['office_phone'] }}</a>
          </p>
        @endif
      </div>

      {{-- Quick facts box --}}
      @if ($showStats && !empty($statItems))
      <div class="ap-sidebar-stats">
        <p class="eyebrow" style="margin-bottom:12px">{{ __('Performance', 'acreline') }}</p>
        @foreach ($statItems as $stat)
          <div class="ap-sidebar-stat">
            <strong>{{ $stat['val'] }}</strong>
            <span>{{ $stat['lbl'] }}</span>
          </div>
        @endforeach
      </div>
      @endif

    </aside>

  </div>{{-- /.ap-profile-grid --}}
</section>

{{-- ── How the walk works (3-card explainer) ──────────────────────────────── --}}
<section class="section section-alt" aria-labelledby="ap-walk-heading">
  <div class="wrap">
    <header class="section-head left reveal">
      <p class="eyebrow">{{ __('What to expect', 'acreline') }}</p>
      <h2 id="ap-walk-heading">{{ sprintf(__('Working with %s', 'acreline'), $agent['name']) }}</h2>
      <p>{{ __('Rural property takes more than 20 minutes. Here is how a sample showing actually runs.', 'acreline') }}</p>
    </header>
    <div class="scan-grid cols-3 reveal">
      <article class="scan-card">
        <span class="num">{{ __('Specialty', 'acreline') }}</span>
        <h3>{{ $agent['specialties'] ?: __('Farms, houses, and land', 'acreline') }}</h3>
        <ul>
          <li>{{ $agent['service_areas'] ?: __('Sample County townships and nearby boroughs', 'acreline') }}</li>
          <li>{{ __('Boots-on-ground before any offer', 'acreline') }}</li>
          <li>{{ __('Honest notes on wet corners and rollback risk', 'acreline') }}</li>
        </ul>
      </article>
      <article class="scan-card">
        <span class="num">{{ __('On the walk', 'acreline') }}</span>
        <h3>{{ __('What gets checked', 'acreline') }}</h3>
        <ul>
          <li>{{ __('Access, well, and septic feasibility', 'acreline') }}</li>
          <li>{{ __('Zoning and easements before an offer', 'acreline') }}</li>
          <li>{{ __('Barn, shop, or historic systems if applicable', 'acreline') }}</li>
        </ul>
      </article>
      <article class="scan-card">
        <span class="num">{{ __('Next step', 'acreline') }}</span>
        <h3>{{ __('Stay moving', 'acreline') }}</h3>
        <ul>
          <li><a href="{{ home_url('/book/') }}">{{ __('Book a showing', 'acreline') }}</a> {{ __('with a sample address', 'acreline') }}</li>
          <li>{{ __('Read the', 'acreline') }} <a href="{{ home_url('/guide') }}">{{ __('buyer guide', 'acreline') }}</a> {{ __('for perc and Act 319', 'acreline') }}</li>
          <li>{{ __('Browse', 'acreline') }} <a href="{{ home_url('/listings') }}">{{ __('listings', 'acreline') }}</a> {{ __('assigned below', 'acreline') }}</li>
        </ul>
      </article>
    </div>
  </div>
</section>

{{-- ── FAQ ──────────────────────────────────────────────────────────────────── --}}
@include('partials.faq-list', [
  'faqTitle' => __('Agent questions', 'acreline'),
  'faqText' => sprintf(__('How to reach %s — and what is sample / fictional.', 'acreline'), $agent['name']),
  'faqHeadClass' => 'left',
  'faqSectionClass' => '',
])

{{-- ── Agent's listings ────────────────────────────────────────────────────── --}}
@if ($listings)
<section class="section section-alt" aria-labelledby="ap-listings-heading">
  <div class="wrap">
    <header class="section-head left">
      <p class="eyebrow">{{ __('Current inventory', 'acreline') }}</p>
      <h2 id="ap-listings-heading">{{ sprintf(__('Listings with %s', 'acreline'), $agent['name']) }}</h2>
    </header>
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
    <p style="margin-top:24px;text-align:center">
      <a class="btn btn-outline" href="{{ home_url('/listings') }}">{{ __('Browse all listings', 'acreline') }}</a>
    </p>
  </div>
</section>
@endif

@else
  <div class="wrap section">
    <p>{{ __('Agent not found.', 'acreline') }}</p>
    <a href="{{ home_url('/agents') }}" class="btn btn-outline">{{ __('Back to agents', 'acreline') }}</a>
  </div>
@endif
@endsection
