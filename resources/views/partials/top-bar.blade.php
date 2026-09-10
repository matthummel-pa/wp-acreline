{{--
  Top bar — desktop only.
  Mobile equivalent is rendered inside the mobile-nav panel (see sections/header.blade.php).
  $tb = $identity['topBar']  (array|false)
--}}
@php
  $tb = $identity['topBar'] ?? false;
  if (! $tb) { return; }

  /* Inline SVG icons for social platforms and contact items. */
  $socialSvg = [
    'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
    'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
    'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98 9.75,15.02" fill="#fff"/></svg>',
    'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
    'x'         => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.732-8.84L1.77 2.25h6.877l4.259 5.63 5.338-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
  ];
  $phoneSvg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.8 19.8 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>';
  $emailSvg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
  $addrSvg    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>';
  $clockSvg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>';
  $closeSvg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>';
  $arrowSvg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9,18 15,12 9,6"/></svg>';
@endphp

<div class="top-bar top-bar--{{ $tb['style'] }}" id="topBar" role="complementary" aria-label="{{ esc_attr__('Site-wide announcement and contact bar', 'acreline') }}">
  <div class="top-bar-inner">

    {{-- ── Left: social icons ──────────────────────────────────────────────── --}}
    @if ($tb['socialIcons'])
      <ul class="top-bar-social" aria-label="{{ esc_attr__('Social links', 'acreline') }}">
        @foreach ($tb['socialIcons'] as $platform => $url)
          <li>
            <a href="{{ esc_url($url) }}" class="top-bar-social-link" target="_blank" rel="noopener noreferrer"
               aria-label="{{ esc_attr(ucfirst($platform === 'x' ? 'X / Twitter' : $platform)) }}">
              {!! $socialSvg[$platform] ?? '' !!}
            </a>
          </li>
        @endforeach
      </ul>
    @endif

    {{-- ── Centre: announcement ────────────────────────────────────────────── --}}
    @if ($tb['message'])
      <p class="top-bar-announcement" role="status">
        @if ($tb['badge'])
          <span class="top-bar-badge">{{ $tb['badge'] }}</span>
        @endif
        @if ($tb['messageUrl'])
          <a href="{{ esc_url($tb['messageUrl']) }}" class="top-bar-msg-link">
            {{ $tb['message'] }}{!! $arrowSvg !!}
          </a>
        @else
          <span>{{ $tb['message'] }}</span>
        @endif
      </p>
    @endif

    {{-- ── Right: contact items + optional CTA ────────────────────────────── --}}
    <div class="top-bar-contacts" aria-label="{{ esc_attr__('Contact information', 'acreline') }}">
      @if ($tb['showPhone'])
        <a href="{{ esc_url($tb['phoneHref']) }}" class="top-bar-item"
           aria-label="{{ esc_attr(sprintf(__('Call %s', 'acreline'), $tb['phone'])) }}">
          {!! $phoneSvg !!}<span>{{ $tb['phone'] }}</span>
        </a>
      @endif

      @if ($tb['showEmail'])
        <a href="mailto:{{ esc_attr($tb['email']) }}" class="top-bar-item"
           aria-label="{{ esc_attr(sprintf(__('Email %s', 'acreline'), $tb['email'])) }}">
          {!! $emailSvg !!}<span>{{ $tb['email'] }}</span>
        </a>
      @endif

      @if ($tb['showAddress'])
        <span class="top-bar-item">
          {!! $addrSvg !!}<span>{{ str_replace("\n", ', ', $tb['address']) }}</span>
        </span>
      @endif

      @if ($tb['showHours'])
        <span class="top-bar-item top-bar-item--hours">
          {!! $clockSvg !!}
          <span>{{ strtok($tb['hours'], "\n") }}</span>
        </span>
      @endif

      @if ($tb['ctaLabel'] && $tb['ctaUrl'])
        <a href="{{ esc_url($tb['ctaUrl']) }}" class="top-bar-cta">{{ $tb['ctaLabel'] }}</a>
      @endif
    </div>

    {{-- ── Dismiss button (optional) ──────────────────────────────────────── --}}
    @if ($tb['dismissible'])
      <button type="button" class="top-bar-close" id="topBarClose"
              aria-label="{{ esc_attr__('Dismiss this bar', 'acreline') }}">
        {!! $closeSvg !!}
      </button>
    @endif

  </div>
</div>
