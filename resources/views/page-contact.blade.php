{{--
  Template Name: Contact
--}}

@extends('layouts.app')

@section('content')
@include('partials.breadcrumbs')
@php(the_content())

{{-- ── Trust strip: response promise ──────────────────────────────────────── --}}
<section class="section section-alt contact-trust-strip" aria-label="{{ __('Contact commitments', 'acreline') }}">
  <div class="wrap">
    <ul class="contact-trust-list" role="list">
      <li>
        <span class="contact-trust-icon" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l3 3"/></svg>
        </span>
        <div>
          <strong>{{ __('Same-day reply', 'acreline') }}</strong>
          <span>{{ __('Messages sent before 4 PM on a business day get a same-day response from a real agent.', 'acreline') }}</span>
        </div>
      </li>
      <li>
        <span class="contact-trust-icon" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2l2.1 4.3 4.7.7-3.4 3.3.8 4.7L10 12.7l-4.2 2.3.8-4.7L3.2 7l4.7-.7z"/></svg>
        </span>
        <div>
          <strong>{{ __('Specialist matched', 'acreline') }}</strong>
          <span>{{ __('Your inquiry is routed to the agent who specialises in your area and property type — not whoever is next in the queue.', 'acreline') }}</span>
        </div>
      </li>
      <li>
        <span class="contact-trust-icon" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V7l6-5 6 5v12"/><path d="M9 19v-6h2v6"/></svg>
        </span>
        <div>
          <strong>{{ __('No obligation', 'acreline') }}</strong>
          <span>{{ __('Reaching out does not start a sales process. Ask questions, compare options, and decide at your own pace.', 'acreline') }}</span>
        </div>
      </li>
      <li>
        <span class="contact-trust-icon" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="16" height="11" rx="2"/><path d="M2 7l8 5 8-5"/></svg>
        </span>
        <div>
          <strong>{{ __('Private inbox', 'acreline') }}</strong>
          <span>{{ __('Your contact details stay with this office. No third-party lead sharing, no spam, no automated drip campaigns.', 'acreline') }}</span>
        </div>
      </li>
    </ul>
  </div>
</section>
@endsection
