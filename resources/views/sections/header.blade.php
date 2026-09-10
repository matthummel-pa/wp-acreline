@php
  $nav = $primaryNav ?? [];
  $bookUrl = $identity['bookUrl'] ?? home_url('/book/');
  $phone = $identity['phone'] ?? '(555) 010-0455';
  $phoneHref = $identity['phoneHref'] ?? 'tel:+15550100455';
  $brand = $identity['brand'] ?? 'Acreline';
  $tagline = $identity['tagline'] ?? 'Farms · land · historic homes';
  $cta = $identity['ctaLabel'] ?? 'Book a showing';
@endphp

<a href="#main" class="skip-link">{{ __('Skip to main content', 'acreline') }}</a>
@include('partials.top-bar')
<header class="site-header {{ $identity['headerClass'] ?? 'is-sticky' }}">
  <div class="header-inner">
    @if (! empty($identity['hasLogo']))
      <div class="brand brand-logo">
        {!! get_custom_logo() !!}
      </div>
    @else
      <a href="{{ home_url('/') }}" class="brand" aria-label="{{ esc_attr($brand) }}">
        <svg class="brand-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true">
          <rect width="48" height="48" rx="12" fill="var(--ink)"/>
          <path d="M24 10 L40 22 V38 H8 V22 Z" fill="var(--accent)"/>
          <rect x="20" y="26" width="8" height="12" fill="var(--paper)"/>
        </svg>
        <span class="brand-text">
          <strong>{{ $brand }}</strong>
          <span>{{ $tagline }}</span>
        </span>
      </a>
    @endif

    <nav class="main-nav" aria-label="{{ esc_attr__('Primary', 'acreline') }}">
      <ul>
        @foreach ($nav as $item)
          <li>
            <a href="{{ esc_url($item['url']) }}" @if($item['active']) class="is-active" aria-current="page" @endif>{{ $item['label'] }}</a>
          </li>
        @endforeach
      </ul>
    </nav>

    <div class="header-cta">
      <a class="header-phone" href="{{ esc_url($phoneHref) }}" aria-label="{{ esc_attr(sprintf(__('Call %s', 'acreline'), $phone)) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>
        <span class="header-phone-num" aria-hidden="true">{{ $phone }}</span>
      </a>
      <a class="btn btn-primary btn-sm" href="{{ esc_url($bookUrl) }}">{{ $cta }}</a>
    </div>

    <button type="button" class="hamburger" id="hamburgerBtn" aria-expanded="false" aria-controls="mobileNav" aria-label="{{ esc_attr__('Open menu', 'acreline') }}">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="nav-backdrop" id="navBackdrop" hidden></div>
<nav class="mobile-nav" id="mobileNav" aria-label="{{ esc_attr__('Site menu', 'acreline') }}" role="dialog" aria-modal="true" aria-hidden="true" hidden>
  <div class="mobile-nav-head">
    <div class="mobile-nav-brand">
      <svg width="32" height="32" viewBox="0 0 48 48" fill="none" aria-hidden="true">
        <rect width="48" height="48" rx="12" fill="var(--ink)"/>
        <path d="M24 10 L40 22 V38 H8 V22 Z" fill="var(--accent)"/>
        <rect x="20" y="26" width="8" height="12" fill="var(--paper)"/>
      </svg>
      <div>
        <p class="mobile-nav-kicker">{{ __('Browse', 'acreline') }}</p>
        <p class="mobile-nav-title">{{ $brand }}</p>
      </div>
    </div>
    <button type="button" class="mobile-nav-close" id="mobileNavClose" aria-label="{{ esc_attr__('Close menu', 'acreline') }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>
  <div class="mobile-nav-links">
    @foreach ($nav as $item)
      <a href="{{ esc_url($item['url']) }}" @if($item['active']) class="is-active" aria-current="page" @endif>
        {{ $item['label'] }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
      </a>
    @endforeach
  </div>
  <div class="mobile-nav-foot">
    @php $tb = $identity['topBar'] ?? false; @endphp
    {{-- Top-bar content surfaces here on mobile when the bar is enabled --}}
    @if ($tb)
      <div class="mnav-tb">
      {{-- Announcement --}}
      @if ($tb['message'])
        <div class="mnav-tb-announcement">
          @if ($tb['badge'])
            <span class="top-bar-badge">{{ $tb['badge'] }}</span>
          @endif
          @if ($tb['messageUrl'])
            <a href="{{ esc_url($tb['messageUrl']) }}" class="mnav-tb-msg-link">{{ $tb['message'] }}</a>
          @else
            <span>{{ $tb['message'] }}</span>
          @endif
        </div>
      @endif

      {{-- Contact items --}}
      <div class="mnav-tb-contacts">
        @if ($tb['showPhone'])
          <a href="{{ esc_url($tb['phoneHref']) }}" class="mnav-tb-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>
            {{ $tb['phone'] }}
          </a>
        @endif
        @if ($tb['showEmail'])
          <a href="mailto:{{ esc_attr($tb['email']) }}" class="mnav-tb-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            {{ $tb['email'] }}
          </a>
        @endif
        @if ($tb['showAddress'])
          <span class="mnav-tb-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>{{ str_replace("\n", ', ', $tb['address']) }}</span>
          </span>
        @endif
        @if ($tb['showHours'])
          <span class="mnav-tb-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            <span>{{ strtok($tb['hours'], "\n") }}</span>
          </span>
        @endif
      </div>

      {{-- Social icons --}}
      @if ($tb['socialIcons'])
        @php
          $socialSvgMobile = [
            'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
            'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="#fff"/></svg>',
            'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
            'x'         => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.732-8.84L1.77 2.25h6.877l4.259 5.63 5.338-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
          ];
        @endphp
        <ul class="mnav-tb-social" aria-label="{{ esc_attr__('Social links', 'acreline') }}">
          @foreach ($tb['socialIcons'] as $platform => $url)
            <li>
              <a href="{{ esc_url($url) }}" target="_blank" rel="noopener noreferrer"
                 aria-label="{{ esc_attr(ucfirst($platform === 'x' ? 'X / Twitter' : $platform)) }}">
                {!! $socialSvgMobile[$platform] ?? '' !!}
              </a>
            </li>
          @endforeach
        </ul>
      @endif

      {{-- CTA --}}
      @if ($tb['ctaLabel'] && $tb['ctaUrl'])
        <a class="top-bar-cta mnav-tb-cta" href="{{ esc_url($tb['ctaUrl']) }}">{{ $tb['ctaLabel'] }}</a>
      @endif
      </div>

    @else
      {{-- Default: just phone + book CTA when top bar is off --}}
      <a class="header-phone" href="{{ esc_url($phoneHref) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>
        {{ sprintf(__('Call %s', 'acreline'), $phone) }}
      </a>
    @endif
    <a class="btn btn-primary" href="{{ esc_url($bookUrl) }}">{{ $cta }}</a>
  </div>
</nav>
