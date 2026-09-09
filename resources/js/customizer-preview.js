/* global wp, ACRELINE_FONTS */
(function () {
  if (!wp || !wp.customize || !window.ACRELINE_FONTS) return;

  var stacks = ACRELINE_FONTS.stacks || {};
  var google = ACRELINE_FONTS.google || {};
  var roles = ACRELINE_FONTS.roles || {};

  function loadGoogleFont(key) {
    var family = google[key];
    if (!family) return;
    var id = "ks-preview-font-" + key;
    if (document.getElementById(id)) return;
    var link = document.createElement("link");
    link.id = id;
    link.rel = "stylesheet";
    link.href = "https://fonts.googleapis.com/css2?family=" + family + "&display=swap";
    document.head.appendChild(link);
  }

  Object.keys(roles).forEach(function (role) {
    wp.customize("ks_font_" + role, function (setting) {
      setting.bind(function (value) {
        var stack = stacks[value];
        if (!stack) return;
        loadGoogleFont(value);
        document.documentElement.style.setProperty(roles[role], stack);
      });
    });
  });

  wp.customize("ks_font_size", function (setting) {
    setting.bind(function (value) {
      document.documentElement.style.setProperty("--font-size-base", parseInt(value, 10) + "px");
    });
  });

  wp.customize("ks_heading_weight", function (setting) {
    setting.bind(function (value) {
      document.documentElement.style.setProperty("--fw-heading", String(value));
    });
  });

  wp.customize("ks_hero_ken_burns", function (setting) {
    setting.bind(function (value) {
      document.body.classList.toggle("ken-burns-enabled", !!value);
    });
  });

  wp.customize("ks_hero_search_tilt", function (setting) {
    setting.bind(function (value) {
      document.body.classList.toggle("hero-search-tilt-enabled", !!value);
      document.dispatchEvent(new CustomEvent("acreline:hero-tilt"));
    });
  });
})();
