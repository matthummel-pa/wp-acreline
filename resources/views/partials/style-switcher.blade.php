@php
  $schemes = \App\Support\ColorSchemes::all();
  $current = $identity['colorScheme'] ?? \App\Support\ColorSchemes::currentKey();
@endphp
<aside class="style-switcher" id="styleSwitcher">
  <button type="button" class="style-switcher-toggle" aria-expanded="false" aria-controls="styleSwitcherPanel">
    <span class="style-switcher-dots" aria-hidden="true">
      @foreach (array_slice($schemes, 0, 4) as $scheme)
        <span class="style-switcher-dot" style="background:{{ $scheme['accent'] }}"></span>
      @endforeach
    </span>
    <span>{{ __('Colors', 'acreline') }}</span>
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
    <p class="style-switcher-hint">{{ __('Set a style under Customize → Colors or Appearance → Acreline Settings. Turn this chip off there when you ship a buyer site.', 'acreline') }}</p>
  </div>
</aside>
