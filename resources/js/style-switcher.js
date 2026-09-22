(function () {
  "use strict";

  var root = document.getElementById("styleSwitcher");
  if (!root) return;

  var toggle = root.querySelector(".style-switcher-toggle");
  var panel = root.querySelector(".style-switcher-panel");
  var buttons = root.querySelectorAll("[data-scheme]");
  var schemes = (window.ACRELINE && window.ACRELINE.schemes) || {};
  document.addEventListener("acreline:ready", function () {
    schemes = (window.ACRELINE && window.ACRELINE.schemes) || schemes;
  });
  var storageKey = "ks-color-scheme";

  function applyScheme(key) {
    var scheme = schemes[key];
    if (!scheme) return;
    document.documentElement.setAttribute("data-scheme", key);
    var tag = document.getElementById("keystone-scheme-preview");
    if (!tag) {
      tag = document.createElement("style");
      tag.id = "keystone-scheme-preview";
      document.head.appendChild(tag);
    }
    tag.textContent = scheme.css || "";

    buttons.forEach(function (btn) {
      var on = btn.getAttribute("data-scheme") === key;
      btn.setAttribute("aria-pressed", on ? "true" : "false");
    });

    try {
      window.localStorage.setItem(storageKey, key);
    } catch (e) {
      /* ignore */
    }

    var url = new URL(window.location.href);
    if (url.searchParams.get("scheme") !== key) {
      url.searchParams.set("scheme", key);
      window.history.replaceState({}, "", url.pathname + url.search + url.hash);
    }
  }

  function requestedScheme() {
    var params = new URLSearchParams(window.location.search);
    var fromUrl = params.get("scheme");
    if (fromUrl && schemes[fromUrl]) return fromUrl;
    try {
      var stored = window.localStorage.getItem(storageKey);
      if (stored && schemes[stored]) return stored;
    } catch (e) {
      /* ignore */
    }
    return "";
  }

  var initial = requestedScheme();
  if (initial) applyScheme(initial);

  if (toggle && panel) {
    toggle.addEventListener("click", function () {
      var open = !panel.hidden;
      panel.hidden = open;
      toggle.setAttribute("aria-expanded", open ? "false" : "true");
    });
  }

  buttons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      applyScheme(btn.getAttribute("data-scheme"));
    });
  });
})();
