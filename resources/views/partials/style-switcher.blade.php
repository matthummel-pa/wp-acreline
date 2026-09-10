@php
  $schemes = \App\Support\ColorSchemes::all();
  $current = $identity['colorScheme'] ?? \App\Support\ColorSchemes::currentKey();
@endphp
<aside class="style-switcher" id="styleSwitcher">
  <button type="button" class="style-switcher-toggle" aria-expanded="false" aria-controls="styleSwitcherPanel">
    {{ __('Colors', 'acreline') }}
  </button>
  <div class="style-switcher-panel" id="styleSwitcherPanel" hidden>
    <p class="style-switcher-label" id="styleSwitcherLabel">{{ __('Try a color style', 'acreline') }}</p>
    <div class="style-switcher-swatches" role="group" aria-labelledby="styleSwitcherLabel">
      @foreach ($schemes as $key => $scheme)
        <button
          type="button"
          class="style-switcher-swatch"
          data-scheme="{{ $key }}"
          aria-pressed="{{ $key === $current ? 'true' : 'false' }}"
          title="{{ $scheme['label'] }}"
        >
          <span class="style-switcher-chip" style="background:{{ $scheme['accent'] }}" aria-hidden="true"></span>
          <span>{{ $scheme['label'] }}</span>
        </button>
      @endforeach
    </div>
    <p class="style-switcher-hint">{{ __('Set a style under Customize → Colors or Appearance → Acreline Settings.', 'acreline') }}</p>
  </div>
</aside>
