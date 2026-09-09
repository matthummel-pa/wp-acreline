@extends('layouts.app')

@section('content')
@php
  $listing = \App\Support\Catalog::listing((int) get_the_ID());
  $agent = $listing && $listing['listing_agent'] ? \App\Support\Catalog::agent((int) $listing['listing_agent']) : null;

  // Theme settings helpers
  $showPPSF   = \App\ks_setting('listing_show_price_per_sqft') !== '0';
  $showDOM    = \App\ks_setting('listing_show_days_on_market') !== '0';
  $showOH     = \App\ks_setting('listing_show_open_house') !== '0';
  $showVT     = \App\ks_setting('listing_show_virtual_tour') !== '0';
  $showVideo  = \App\ks_setting('listing_show_video_tour') !== '0';
  $showFP     = \App\ks_setting('listing_show_floor_plan') !== '0';
  $showPD     = \App\ks_setting('listing_show_property_details') !== '0';
  $showUtil   = \App\ks_setting('listing_show_utilities') !== '0';
  $showHOA    = \App\ks_setting('listing_show_hoa') !== '0';
  $showLand   = \App\ks_setting('listing_show_land_section') !== '0';
  $showSchool = \App\ks_setting('listing_show_school_district') !== '0';
  $showFlood  = \App\ks_setting('listing_show_flood_zone') !== '0';
  $showGreen  = \App\ks_setting('listing_show_green_features') !== '0';
  $showSmart  = \App\ks_setting('listing_show_smart_home') !== '0';
  $showCalc   = \App\ks_setting('show_mortgage_calc') !== '0';
  $rateDefault = (float) \App\ks_setting('mortgage_rate_default', '7.0');

  $labelBeds  = \App\ks_setting('label_beds', __('Beds', 'acreline'));
  $labelBaths = \App\ks_setting('label_baths', __('Baths', 'acreline'));
  $labelSqft  = \App\ks_setting('label_sqft', __('Sq Ft', 'acreline'));
  $labelAcres = \App\ks_setting('label_acres', __('Acres', 'acreline'));
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
    ['href' => home_url('/book/').'?listing_id='.$listing['id'], 'label' => __('Book a showing', 'acreline'), 'class' => 'btn btn-primary'],
    ['href' => home_url('/listings'), 'label' => __('All listings', 'acreline'), 'class' => 'btn btn-outline light'],
  ],
])

{{-- ── Open house banner ──────────────────────────────────────────────────── --}}
@if ($showOH && $listing['open_house_type'] && $listing['open_house_date'])
<div class="listing-open-house" role="status">
  <span class="listing-oh-badge">{{ __('Open House', 'acreline') }}</span>
  <span>{{ \App\Support\Catalog::OPEN_HOUSE_TYPES[$listing['open_house_type']] ?? '' }} &middot; {{ $listing['open_house_date'] }}{{ $listing['open_house_time'] ? ' · '.$listing['open_house_time'] : '' }}</span>
</div>
@endif

<section class="section">
  <div class="wrap listing-single">
    <div class="listing-single-main">

      {{-- ── Hero photo ───────────────────────────────────────────────────── --}}
      <div
        class="listing-single-photo"
        role="img"
        aria-label="{{ esc_attr($listing['title']) }}"
        style="@if ($listing['image']) background-image:url({{ $listing['image'] }});background-size:cover;background-position:center;@else background:{{ $listing['grad'] }};@endif"
      ></div>

      {{-- ── Price & core specs ──────────────────────────────────────────── --}}
      <p class="modal-price">
        {{ \App\Support\Catalog::formatMoney((int) $listing['price']) }}
        @if ($showPPSF && $listing['price_per_sqft'])
          <small class="listing-pppsf">&nbsp;· ${{ number_format($listing['price_per_sqft']) }}/{{ __('sq ft', 'acreline') }}</small>
        @endif
      </p>
      <div class="modal-specs">
        @if ($listing['type'] !== 'land')
          <div><strong>{{ $listing['beds'] }}</strong><span>{{ $labelBeds }}</span></div>
          <div><strong>{{ $listing['baths'] }}</strong><span>{{ $labelBaths }}</span></div>
          @if ($listing['sqft'])
            <div><strong>{{ number_format((int) $listing['sqft']) }}</strong><span>{{ $labelSqft }}</span></div>
          @endif
        @endif
        <div><strong>{{ $listing['acres'] }}</strong><span>{{ $labelAcres }}</span></div>
        @if ($listing['year_built'])
          <div><strong>{{ $listing['year_built'] }}</strong><span>{{ __('Year built', 'acreline') }}</span></div>
        @endif
        @if ($showDOM && $listing['days_on_market'])
          <div><strong>{{ $listing['days_on_market'] }}</strong><span>{{ __('Days on market', 'acreline') }}</span></div>
        @endif
      </div>

      {{-- ── Description ─────────────────────────────────────────────────── --}}
      <div class="prose">
        <p>{{ $listing['desc'] }}</p>
      </div>

      {{-- ── Media row: tour buttons ─────────────────────────────────────── --}}
      @if (($showVT && $listing['virtual_tour']) || ($showVideo && $listing['video_tour']))
      <div class="listing-tour-row">
        @if ($showVT && $listing['virtual_tour'])
          <a class="btn btn-outline btn-sm" href="{{ $listing['virtual_tour'] }}" target="_blank" rel="noopener">
            &#x1F3E0; {{ __('Virtual tour', 'acreline') }}
          </a>
        @endif
        @if ($showVideo && $listing['video_tour'])
          <a class="btn btn-outline btn-sm" href="{{ $listing['video_tour'] }}" target="_blank" rel="noopener">
            &#x25B6; {{ __('Video tour', 'acreline') }}
          </a>
        @endif
      </div>
      @endif

      {{-- ── Floor plan ───────────────────────────────────────────────────── --}}
      @if ($showFP && $listing['floor_plan'])
      <div class="listing-floor-plan">
        <h3>{{ __('Floor plan', 'acreline') }}</h3>
        <a href="{{ $listing['floor_plan'] }}" target="_blank" rel="noopener">
          <img src="{{ $listing['floor_plan'] }}" alt="{{ esc_attr(__('Floor plan', 'acreline').' — '.$listing['title']) }}" loading="lazy" class="listing-floor-plan-img">
        </a>
      </div>
      @endif

      {{-- ── Property details panel ───────────────────────────────────────── --}}
      @if ($showPD)
      @php
        $pdItems = array_filter([
          __('Condition', 'acreline') => $listing['condition'] ? (\App\Support\Catalog::PROPERTY_CONDITIONS[$listing['condition']] ?? $listing['condition']) : '',
          __('Garage', 'acreline') => trim(($listing['garage'] ? $listing['garage'].' '.__('space(s)', 'acreline') : '').' '.($listing['garage_type'] ? '('.(\App\Support\Catalog::GARAGE_TYPES[$listing['garage_type']] ?? $listing['garage_type']).')' : '')),
          __('Basement', 'acreline') => $listing['basement'] ? (\App\Support\Catalog::BASEMENT_TYPES[$listing['basement']] ?? $listing['basement']) : '',
          __('Heating', 'acreline') => $listing['heating'],
          __('Cooling', 'acreline') => $listing['cooling'],
          __('Zoning', 'acreline') => $listing['zoning'],
          __('Lot features', 'acreline') => $listing['lot_features'],
          __('View', 'acreline') => $listing['view'] ? (\App\Support\Catalog::VIEWS[$listing['view']] ?? $listing['view']) : '',
          __('Historic designation', 'acreline') => $listing['historic_designation'],
        ]);
      @endphp
      @if (!empty($pdItems))
      <div class="listing-detail-panel">
        <h3>{{ __('Property details', 'acreline') }}</h3>
        <dl class="listing-detail-grid">
          @foreach ($pdItems as $pdLabel => $pdValue)
            @if ($pdValue)
              <div><dt>{{ $pdLabel }}</dt><dd>{{ $pdValue }}</dd></div>
            @endif
          @endforeach
          @if ($showSchool && $listing['school_district'])
            <div><dt>{{ __('School district', 'acreline') }}</dt><dd>{{ $listing['school_district'] }}</dd></div>
          @endif
          @if ($showFlood && $listing['flood_zone'])
            <div><dt>{{ __('Flood zone', 'acreline') }}</dt><dd>{{ $listing['flood_zone'] }}</dd></div>
          @endif
        </dl>
      </div>
      @endif
      @endif

      {{-- ── Utilities panel ──────────────────────────────────────────────── --}}
      @if ($showUtil && ($listing['water'] || $listing['sewer'] || $listing['heating'] || $listing['cooling']))
      <div class="listing-detail-panel">
        <h3>{{ __('Utilities', 'acreline') }}</h3>
        <dl class="listing-detail-grid">
          @if ($listing['water'])<div><dt>{{ __('Water', 'acreline') }}</dt><dd>{{ $listing['water'] }}</dd></div>@endif
          @if ($listing['sewer'])<div><dt>{{ __('Sewer / septic', 'acreline') }}</dt><dd>{{ $listing['sewer'] }}</dd></div>@endif
        </dl>
      </div>
      @endif

      {{-- ── HOA & finances ────────────────────────────────────────────────── --}}
      @if ($showHOA && ($listing['property_tax'] || $listing['hoa'] || $listing['hoa_monthly']))
      <div class="listing-detail-panel">
        <h3>{{ __('Finances', 'acreline') }}</h3>
        <dl class="listing-detail-grid">
          @if ($listing['property_tax'])<div><dt>{{ __('Annual taxes', 'acreline') }}</dt><dd>{{ $listing['property_tax'] }}</dd></div>@endif
          @if ($listing['hoa_monthly'])<div><dt>{{ __('HOA monthly', 'acreline') }}</dt><dd>{{ $listing['hoa_monthly'] }}</dd></div>@endif
          @if ($listing['hoa'])<div><dt>{{ __('HOA notes', 'acreline') }}</dt><dd>{{ $listing['hoa'] }}</dd></div>@endif
          @if ($listing['hoa_amenities'])<div><dt>{{ __('HOA amenities', 'acreline') }}</dt><dd>{{ $listing['hoa_amenities'] }}</dd></div>@endif
        </dl>
      </div>
      @endif

      {{-- ── Land & farm details ───────────────────────────────────────────── --}}
      @if ($showLand)
      @php
        $landItems = array_filter([
          __('Tillable acres', 'acreline') => $listing['tillable_acres'],
          __('Pasture acres', 'acreline') => $listing['pasture_acres'],
          __('Crop / hay acres', 'acreline') => $listing['crop_acres'],
          __('Outbuildings', 'acreline') => $listing['outbuildings'],
          __('Mineral rights', 'acreline') => $listing['mineral_rights'] ? (\App\Support\Catalog::RIGHTS_OPTIONS[$listing['mineral_rights']] ?? $listing['mineral_rights']) : '',
          __('Water rights', 'acreline') => $listing['water_rights'] ? (\App\Support\Catalog::RIGHTS_OPTIONS[$listing['water_rights']] ?? $listing['water_rights']) : '',
          __('Conservation easement', 'acreline') => $listing['conservation_easement'],
        ]);
      @endphp
      @if (!empty($landItems))
      <div class="listing-detail-panel">
        <h3>{{ __('Land &amp; farm details', 'acreline') }}</h3>
        <dl class="listing-detail-grid">
          @foreach ($landItems as $lLabel => $lValue)
            @if ($lValue)
              <div><dt>{{ $lLabel }}</dt><dd>{{ $lValue }}</dd></div>
            @endif
          @endforeach
        </dl>
      </div>
      @endif
      @endif

      {{-- ── Feature chips ─────────────────────────────────────────────────── --}}
      @php
        $chips = array_filter([
          $showGreen && $listing['green_features'] ? $listing['green_features'] : '',
          $showSmart && $listing['smart_home'] ? $listing['smart_home'] : '',
        ]);
      @endphp
      @if (!empty($chips))
      <div class="listing-feature-chips">
        @if ($showGreen && $listing['green_features'])
          @foreach (array_filter(array_map('trim', explode(',', $listing['green_features']))) as $chip)
            <span class="listing-chip listing-chip-green">&#9677; {{ $chip }}</span>
          @endforeach
        @endif
        @if ($showSmart && $listing['smart_home'])
          @foreach (array_filter(array_map('trim', explode(',', $listing['smart_home']))) as $chip)
            <span class="listing-chip listing-chip-smart">&#x26A1; {{ $chip }}</span>
          @endforeach
        @endif
      </div>
      @endif

      {{-- ── Mortgage calculator ───────────────────────────────────────────── --}}
      @if ($showCalc && $listing['price'] > 0)
      <div class="listing-calc" id="listing-calc">
        <h3>{{ __('Mortgage calculator', 'acreline') }}</h3>
        <p class="listing-calc-disclaimer">{{ __('Estimate only — not a loan offer. Consult a licensed lender.', 'acreline') }}</p>
        <div class="listing-calc-row">
          <label for="calc-price">{{ __('Home price', 'acreline') }}</label>
          <input type="number" id="calc-price" class="calc-input" value="{{ (int) $listing['price'] }}" min="10000" step="1000">
        </div>
        <div class="listing-calc-row">
          <label for="calc-down">{{ __('Down payment (%)', 'acreline') }}</label>
          <input type="number" id="calc-down" class="calc-input" value="20" min="0" max="100" step="1">
        </div>
        <div class="listing-calc-row">
          <label for="calc-rate">{{ __('Interest rate (%)', 'acreline') }}</label>
          <input type="number" id="calc-rate" class="calc-input" value="{{ $rateDefault }}" min="0.1" max="30" step="0.05">
        </div>
        <div class="listing-calc-row">
          <label for="calc-term">{{ __('Loan term (years)', 'acreline') }}</label>
          <select id="calc-term" class="calc-input">
            <option value="30" selected>30</option>
            <option value="20">20</option>
            <option value="15">15</option>
            <option value="10">10</option>
          </select>
        </div>
        <div class="listing-calc-result" aria-live="polite">
          <span>{{ __('Est. monthly payment', 'acreline') }}</span>
          <strong id="calc-result">—</strong>
        </div>
      </div>
      <script>
      (function(){
        var els = {
          price: document.getElementById('calc-price'),
          down: document.getElementById('calc-down'),
          rate: document.getElementById('calc-rate'),
          term: document.getElementById('calc-term'),
          result: document.getElementById('calc-result'),
        };
        function calc() {
          var p = parseFloat(els.price.value) * (1 - parseFloat(els.down.value) / 100);
          var r = parseFloat(els.rate.value) / 100 / 12;
          var n = parseInt(els.term.value, 10) * 12;
          if (!p || !n || isNaN(r) || isNaN(p)) { els.result.textContent = '—'; return; }
          var m = r === 0 ? p / n : p * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
          els.result.textContent = '$' + m.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }
        [els.price, els.down, els.rate, els.term].forEach(function(el) { el && el.addEventListener('input', calc); });
        calc();
      })();
      </script>
      @endif

      <div class="cta-actions" style="margin-top:32px">
        <a class="btn btn-primary" href="{{ home_url('/book/') }}?listing_id={{ $listing['id'] }}">{{ __('Book a showing', 'acreline') }}</a>
        <a class="btn btn-outline" href="{{ home_url('/listings') }}">{{ __('All listings', 'acreline') }}</a>
      </div>
    </div>

    {{-- ── Sidebar: agent card ─────────────────────────────────────────────── --}}
    @if ($agent)
      <aside class="listing-agent-card">
        <p class="eyebrow">{{ __('Listing agent', 'acreline') }}</p>
        @if ($agent['photo'])
          <img class="agent-avatar-photo" src="{{ $agent['photo'] }}" width="80" height="80" alt="{{ esc_attr($agent['name']) }}" loading="lazy">
        @endif
        <h2><a href="{{ $agent['permalink'] }}">{{ $agent['name'] }}</a></h2>
        <p class="agent-title">{{ $agent['job_title'] }}</p>
        @if ($agent['featured_badge'])
          <span class="agent-badge">{{ $agent['featured_badge'] }}</span>
        @endif
        <p>{{ mb_strimwidth($agent['bio'], 0, 120, '…') }}</p>
        @if ($agent['phone'])
          <a class="agent-phone" href="{{ \App\Support\Catalog::telHref($agent['phone']) }}">{{ $agent['phone'] }}</a>
        @endif
        @if ($agent['calendly'] && \App\ks_setting('agent_show_calendly') !== '0')
          <a class="btn btn-outline btn-sm" href="{{ $agent['calendly'] }}" target="_blank" rel="noopener" style="margin-top:8px">{{ __('Schedule a call', 'acreline') }}</a>
        @endif
      </aside>
    @endif
  </div>
</section>
@endif
@endsection
