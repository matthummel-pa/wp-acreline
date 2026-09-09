{{--
  Template Name: Areas
--}}

@extends('layouts.app')

@section('content')
@include('partials.breadcrumbs')
@php(the_content())

{{-- ── Area quick-compare table ─────────────────────────────────────────── --}}
<section class="section areas-compare-section" aria-labelledby="areas-compare-heading">
  <div class="wrap">
    <header class="section-head left reveal">
      <p class="eyebrow">{{ __('Side-by-side', 'acreline') }}</p>
      <h2 id="areas-compare-heading">{{ __('Area comparison at a glance', 'acreline') }}</h2>
      <p>{{ __('Typical ranges for sample concept parcels — actual prices vary by size, condition, and seasonal market.', 'acreline') }}</p>
    </header>
    <div class="areas-compare-wrap reveal" role="region" aria-label="{{ __('Area comparison table', 'acreline') }}">
      <table class="areas-compare-table" aria-describedby="areas-compare-heading">
        <thead>
          <tr>
            <th scope="col">{{ __('Area', 'acreline') }}</th>
            <th scope="col">{{ __('Primary land type', 'acreline') }}</th>
            <th scope="col">{{ __('Typical price range', 'acreline') }}</th>
            <th scope="col">{{ __('Well / septic', 'acreline') }}</th>
            <th scope="col">{{ __('Best for', 'acreline') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Oak Hollow', 'acreline') }}</th>
            <td>{{ __('Century homesteads, orchards', 'acreline') }}</td>
            <td>$290K–$620K</td>
            <td>{{ __('Well + private septic', 'acreline') }}</td>
            <td>{{ __('Historic homes, orchard buyers', 'acreline') }}</td>
          </tr>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Orchard Belt', 'acreline') }}</th>
            <td>{{ __('Fruit ground, cold storage', 'acreline') }}</td>
            <td>$380K–$1.1M</td>
            <td>{{ __('Irrigation well, farm septic', 'acreline') }}</td>
            <td>{{ __('Working farm operators', 'acreline') }}</td>
          </tr>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Mill Creek', 'acreline') }}</th>
            <td>{{ __('Mixed farmland, quiet lots', 'acreline') }}</td>
            <td>$195K–$480K</td>
            <td>{{ __('Well + perc required (raw)', 'acreline') }}</td>
            <td>{{ __('Value buyers, first-time land', 'acreline') }}</td>
          </tr>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Grain Country', 'acreline') }}</th>
            <td>{{ __('Tillable, cash-crop tracts', 'acreline') }}</td>
            <td>$420K–$2.2M</td>
            <td>{{ __('Farm well, grain-yard septic', 'acreline') }}</td>
            <td>{{ __('Farm investors, ag operators', 'acreline') }}</td>
          </tr>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Hill Country', 'acreline') }}</th>
            <td>{{ __('Timber, hunting, cabins', 'acreline') }}</td>
            <td>$110K–$390K</td>
            <td>{{ __('Spring or well, outhouse/perc', 'acreline') }}</td>
            <td>{{ __('Recreational buyers', 'acreline') }}</td>
          </tr>
          <tr>
            <th scope="row" class="ac-area-name">{{ __('Border Farms', 'acreline') }}</th>
            <td>{{ __('Small farms, pasture', 'acreline') }}</td>
            <td>$165K–$440K</td>
            <td>{{ __('Well + private septic', 'acreline') }}</td>
            <td>{{ __('First-time farm buyers', 'acreline') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="areas-compare-disclaimer">
      <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="8"/><path d="M10 9v5M10 7v.5"/></svg>
      {{ __('All figures are sample ranges for concept demonstration only. Not real MLS data or licensed appraisal values.', 'acreline') }}
    </p>
  </div>
</section>
@endsection
