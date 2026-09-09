{{--
  Template Name: Book a showing
--}}

@extends('layouts.app')

@section('content')
@include('partials.breadcrumbs')
@php(the_content())

{{-- ── What to bring checklist ──────────────────────────────────────────── --}}
<section class="section book-prep-section" aria-labelledby="book-prep-heading">
  <div class="wrap book-prep-grid">
    <div class="book-prep-col">
      <h2 id="book-prep-heading" class="book-prep-heading">{{ __('Come prepared', 'acreline') }}</h2>
      <p class="book-prep-lead">{{ __('A rural showing is not a quick drive-through. Here is what makes yours worth the trip.', 'acreline') }}</p>
      <ul class="book-checklist" role="list">
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
          <strong>{{ __('Boots or waterproof shoes', 'acreline') }}</strong>
          <span>{{ __('Farm ground, creek fields, and wooded lots are often wet. A good pair of boots is the single most useful thing you can bring.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
          <strong>{{ __('Your priority list', 'acreline') }}</strong>
          <span>{{ __('Write down the three things that would make or break the purchase. Your agent will address them on site, not in a follow-up email.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
          <strong>{{ __('Financing status', 'acreline') }}</strong>
          <span>{{ __('Know roughly what you are approved for — or what you plan to pay cash. It shapes which parcels make sense to walk.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
          <strong>{{ __('Your timeline', 'acreline') }}</strong>
          <span>{{ __('Are you buying in the next 60 days or researching for next year? Your agent will calibrate the conversation accordingly.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="4 10 8 14 16 6"/></svg>
          <strong>{{ __('All decision-makers', 'acreline') }}</strong>
          <span>{{ __('If a partner, parent, or business partner will be part of the purchase, bring them. An extra showing costs everyone time.', 'acreline') }}</span>
        </li>
      </ul>
    </div>
    <div class="book-prep-col">
      <h2 class="book-prep-heading">{{ __('What your agent brings', 'acreline') }}</h2>
      <p class="book-prep-lead">{{ __('Preparation goes both ways. Your assigned specialist arrives ready.', 'acreline') }}</p>
      <ul class="book-checklist book-checklist--accent" role="list">
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
          <strong>{{ __('Property briefing', 'acreline') }}</strong>
          <span>{{ __('Parcel map, deed history, tax enrollment status, well log if available, and any disclosed issues — ready before you arrive.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
          <strong>{{ __('Comps and price context', 'acreline') }}</strong>
          <span>{{ __('Recent sales of similar ground in the same area, so you understand what the asking price reflects.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
          <strong>{{ __('On-site answers', 'acreline') }}</strong>
          <span>{{ __('Questions about drainage, soil quality, zoning, or septic feasibility answered on the walk — not in a follow-up email three days later.', 'acreline') }}</span>
        </li>
        <li>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
          <strong>{{ __('No pressure close', 'acreline') }}</strong>
          <span>{{ __('The goal of a showing is information — not a signature. Agents do not push offers on the property or "back at the office."', 'acreline') }}</span>
        </li>
      </ul>
    </div>
  </div>
</section>
@endsection
