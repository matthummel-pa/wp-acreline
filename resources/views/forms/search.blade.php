<form role="search" method="get" class="search-form" action="{{ home_url('/') }}">
  <label for="s">
    <span class="search-label">{{ _x('Search', 'label', 'acreline') }}</span>
    <input
      id="s"
      type="search"
      placeholder="{!! esc_attr_x('Search listings, guides…', 'placeholder', 'acreline') !!}"
      value="{{ get_search_query() }}"
      name="s"
    >
  </label>

  <button type="submit">{{ _x('Search', 'submit button', 'acreline') }}</button>
</form>
