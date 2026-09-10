@php
  $bookUrl = $identity['bookUrl'] ?? home_url('/book/');
  $phone = $identity['phone'] ?? '(555) 010-0455';
  $phoneHref = $identity['phoneHref'] ?? 'tel:+15550100455';
  $email = $identity['email'] ?? 'hello@acreline-concept.test';
  $brand = $identity['brand'] ?? 'Acreline';
  $cta = $identity['ctaLabel'] ?? 'Book a showing';
  $explore = $footerNav ?? [];
@endphp

<footer class="site-footer">
  <div class="wrap">
    <div class="footer-cta">
      <div>
        <p class="eyebrow">{{ __('Next step', 'acreline') }}</p>
        <h2>{{ __('Tour a sample home', 'acreline') }}</h2>
        <p>{{ __('Pick a listing, choose a slot, and see how a modern realtor booking flow feels.', 'acreline') }}</p>
      </div>
      <div class="footer-cta-actions">
        <a class="btn btn-primary" href="{{ esc_url($bookUrl) }}">{{ $cta }}</a>
        <a class="btn btn-outline light" href="{{ esc_url($phoneHref) }}">{{ __('Call the office', 'acreline') }}</a>
      </div>
    </div>

    <div class="footer-grid">
      <div class="footer-intro">
        <div class="footer-brand">
          <svg width="34" height="34" viewBox="0 0 48 48" fill="none" aria-hidden="true">
            <rect width="48" height="48" rx="12" fill="var(--accent)"/>
            <path d="M24 10 L40 22 V38 H8 V22 Z" fill="#141210"/>
            <rect x="20" y="26" width="8" height="12" fill="#fffcf7"/>
          </svg>
          <strong>{{ $brand }}</strong>
        </div>
        @if (! empty($identity['footerBlurb']))
          <p>{{ $identity['footerBlurb'] }}</p>
        @endif
        @if (! empty($compliance['showLicenseFooter']))
          @include('partials.compliance-id')
        @endif
        @include('partials.social-links')
      </div>
      <div>
        <h3 class="footer-heading">{{ __('Office', 'acreline') }}</h3>
        <address>
          {!! nl2br(esc_html($identity['address'] ?? '')) !!}<br>
          <a href="{{ esc_url($phoneHref) }}">{{ $phone }}</a><br>
          <a href="mailto:{{ esc_attr($email) }}">{{ $email }}</a>
        </address>
      </div>
      <div>
        <h3 class="footer-heading">{{ __('Hours', 'acreline') }}</h3>
        <p>{!! nl2br(esc_html($identity['hours'] ?? '')) !!}</p>
      </div>
      <nav aria-labelledby="footer-links-heading">
        <h3 class="footer-heading" id="footer-links-heading">{{ __('Explore', 'acreline') }}</h3>
        <ul class="footer-links">
          @foreach ($explore as $item)
            <li><a href="{{ esc_url($item['url']) }}" @if(! empty($item['active'])) aria-current="page" @endif>{{ $item['label'] }}</a></li>
          @endforeach
        </ul>
      </nav>
    </div>

    @if (is_active_sidebar('sidebar-footer'))
      <div class="footer-widgets">
        @php(dynamic_sidebar('sidebar-footer'))
      </div>
    @endif

    @if (! empty($identity['showDemoChrome']))
      <p class="footer-service-area">{{ __('Serving a fictional Sample County market for design demonstration purposes.', 'acreline') }}</p>
    @endif
    <div class="footer-bottom">
      @if (! empty($compliance['ehoEnabled']))
        <div class="equal-housing">
          @if (! empty($compliance['ehoShowLogo']))
            <svg class="equal-housing__mark" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
              <path fill="currentColor" d="M16 4L4 14h3v12h6v-7h6v7h6V14h3L16 4zm-1.2 9.2h2.4v1.4h-2.4V13.2zm0 2.6h2.4V20h-2.4v-4.2z"/>
            </svg>
          @endif
          <div class="equal-housing__copy">
            <strong>{{ $compliance['ehoLabel'] ?? __('Equal Housing Opportunity', 'acreline') }}</strong>
            @if (! empty($compliance['ehoStatement']))
              <p>{{ $compliance['ehoStatement'] }}</p>
            @endif
          </div>
        </div>
      @endif
      <p>
        &copy; <span data-year>{{ date('Y') }}</span> {{ $compliance['licensedName'] ?? $brand }}
        @if (! empty($compliance['privacyUrl']))
          · <a href="{{ esc_url($compliance['privacyUrl']) }}">{{ __('Privacy', 'acreline') }}</a>
        @endif
        @if (! empty($compliance['termsUrl']))
          · <a href="{{ esc_url($compliance['termsUrl']) }}">{{ __('Terms', 'acreline') }}</a>
        @endif
        @if (! empty($identity['showCredit']))
          @if (! empty($identity['creditUrl']))
            · <a href="{{ esc_url($identity['creditUrl']) }}" rel="nofollow noopener">{{ $identity['creditText'] }}</a>
          @else
            · {{ $identity['creditText'] }}
          @endif
        @endif
      </p>
    </div>
  </div>
</footer>
