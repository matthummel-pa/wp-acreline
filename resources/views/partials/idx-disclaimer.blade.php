@php
  $c = $compliance ?? [];
  $listing = $listing ?? [];
  $office = trim((string) ($listing['listing_office'] ?? ''));
  if ($office === '' && ! empty($c['idxAttribution'])) {
    $office = (string) ($c['brokerageLegalName'] ?? '');
  }
  $disclaimer = (string) ($c['idxDisclaimer'] ?? '');
  $copyright = (string) ($c['idxCopyright'] ?? '');
  $logo = (string) ($c['idxLogoUrl'] ?? '');
  $updated = trim((string) ($listing['updated'] ?? ''));
  $show = ! empty($c['idxEnabled']) && (
    $disclaimer !== '' || $copyright !== '' || $logo !== ''
    || (! empty($c['idxAttribution']) && $office !== '')
    || $updated !== ''
  );
@endphp
@if ($show)
  <aside class="listing-idx" aria-label="{{ esc_attr__('Listing attribution and MLS disclaimer', 'acreline') }}">
    @if ($logo !== '')
      <img class="listing-idx__logo" src="{{ esc_url($logo) }}" alt="" width="72" height="36" loading="lazy">
    @endif
    @if (! empty($c['idxAttribution']) && $office !== '')
      <p class="listing-idx__office">{{ sprintf(__('Listed by %s', 'acreline'), $office) }}</p>
    @endif
    @if ($disclaimer !== '')
      <p class="listing-idx__disclaimer">{{ $disclaimer }}</p>
    @endif
    @if ($copyright !== '')
      <p class="listing-idx__copy">{{ $copyright }}</p>
    @endif
    @if ($updated !== '')
      <p class="listing-idx__updated">{{ sprintf(__('Last updated %s', 'acreline'), $updated) }}</p>
    @endif
  </aside>
@endif
