@php
  $c = $compliance ?? [];
  $inputId = $inputId ?? 'leadConsent';
@endphp
@if (! empty($c['consentEnabled']))
  <div class="field field-span field-consent">
    <input type="checkbox" id="{{ $inputId }}" name="lead_consent" value="1" required>
    <label for="{{ $inputId }}">
      <span>{{ $c['consentDisclosure'] ?? '' }}</span>
      @if (! empty($c['consentSms']))
        <span class="field-consent__extra">{{ $c['consentSms'] }}</span>
      @endif
    </label>
  </div>
@endif
