<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ \App\Support\Identity::accent() }}">
    <meta name="author" content="{{ $identity['brand'] ?? get_bloginfo('name') }}">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
      window.ACRELINE = @json($keystone);
      window.KEYSTONE = window.ACRELINE;
    </script>
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    @if (! empty($identity['showDemoChrome']))
      <p class="demo-banner"><strong>{{ __('Acreline demo', 'acreline') }}</strong> · {{ __('Fiction only', 'acreline') }} · {{ __('Not a live MLS, brokerage or booking system', 'acreline') }}</p>
    @endif

    @include('sections.header')

    <main id="main" class="main">
      @yield('content')
    </main>

    @hasSection('sidebar')
      <aside class="sidebar">
        @yield('sidebar')
      </aside>
    @endif

    @include('sections.footer')
    @include('partials.chat')

    @if (! empty($identity['showDemoChrome']))
      @include('partials.style-switcher')
    @endif

    @if (! empty($identity['showDemoChrome']) && ! empty($identity['creditUrl']))
      <a href="{{ esc_url($identity['creditUrl']) }}" class="concept-badge" rel="nofollow noopener">{{ $identity['creditText'] }}</a>
    @endif

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
