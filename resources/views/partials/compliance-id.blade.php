@php
  $compliance = $compliance ?? [];
  $parts = $compliance['idParts'] ?? [];
  $phone = $identity['phone'] ?? '';
@endphp
@if (! empty($compliance['hasBrokerId']) && $parts !== [])
  <p class="compliance-id" translate="no">
    {{ implode(' · ', $parts) }}
    @if ($phone !== '')
      · <a href="{{ esc_url($identity['phoneHref'] ?? '') }}">{{ $phone }}</a>
    @endif
  </p>
@endif
