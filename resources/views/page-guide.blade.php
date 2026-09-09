{{--
  Template Name: Guide
--}}

@extends('layouts.app')

@section('content')
@include('partials.breadcrumbs')
@php(the_content())

{{-- ── Quick-scan checklist cards ───────────────────────────────────────── --}}
<section class="section guide-checklist-section" aria-labelledby="guide-checklist-heading">
  <div class="wrap">
    <header class="section-head left reveal">
      <p class="eyebrow">{{ __('Before you make an offer', 'acreline') }}</p>
      <h2 id="guide-checklist-heading">{{ __('The rural property checklist', 'acreline') }}</h2>
      <p>{{ __('Eight questions to answer before you fall in love with the view and the price tag.', 'acreline') }}</p>
    </header>
    <ol class="guide-checklist" role="list">
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">01</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Water source — well or municipal?', 'acreline') }}</h3>
          <p>{{ __('Private wells need a yield test and a water-quality report. Ask for the original well log and the most recent test date. A well producing under 3 gpm may not support the use you have planned.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">02</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Septic — existing system or percolation required?', 'acreline') }}</h3>
          <p>{{ __('An existing septic has records on file with the county. Raw land needs a perc test before you can pull a permit. Perc results control what you can build and where.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">03</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Road access — deeded or by permission?', 'acreline') }}</h3>
          <p>{{ __("A private lane that crosses a neighbour's land needs a recorded easement in the deed. \"We've always used that road\" is not legal access and will show up in a title search.", 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">04</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Zoning and agricultural enrollments', 'acreline') }}</h3>
          <p>{{ __('Land enrolled in Act 319 (Clean and Green) or under an agricultural conservation easement has use restrictions. Rollback taxes can be triggered by certain improvements. Verify enrollment status with the county before closing.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">05</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Survey — does one exist?', 'acreline') }}</h3>
          <p>{{ __('Many rural parcels have never been surveyed. Boundary pins may be missing or disputed. If the parcel shape or acreage matters to your use plan, budget for a fresh survey.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">06</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Flood zone and drainage', 'acreline') }}</h3>
          <p>{{ __('Check the FEMA flood map. Creek-bottom and low-lying fields may be in Zone A. Flood insurance is required for federally-backed loans on Zone A parcels and premiums can be significant.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">07</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Mineral rights — included or severed?', 'acreline') }}</h3>
          <p>{{ __('In Pennsylvania, mineral rights can be owned separately from the surface. Ask the seller whether oil, gas, and mineral rights are included in the sale and request a title opinion.', 'acreline') }}</p>
        </div>
      </li>
      <li class="guide-checklist__item">
        <span class="guide-checklist__num" aria-hidden="true">08</span>
        <div class="guide-checklist__body">
          <h3>{{ __('Financing — land loan or conventional?', 'acreline') }}</h3>
          <p>{{ __('Standard home mortgages are not available for raw land. Farm Credit, USDA, or local community banks handle most rural loans. Down payment requirements are typically 20–35% and loan terms are shorter than residential.', 'acreline') }}</p>
        </div>
      </li>
    </ol>
    <p style="margin-top:32px;text-align:center">
      <a class="btn btn-primary" href="{{ home_url('/book/') }}">{{ __('Book a showing', 'acreline') }}</a>
      <a class="btn btn-outline" href="{{ home_url('/listings') }}" style="margin-left:12px">{{ __('Browse listings', 'acreline') }}</a>
    </p>
  </div>
</section>
@endsection
