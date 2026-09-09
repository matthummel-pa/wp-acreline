/* =========================================================================
   Acreline — SINGLE LISTING page tools
   - Sticky CTA bar (price + book) after scrolling past hero
   - Share button (Web Share API with URL copy fallback)
   - Recently viewed tracking (localStorage, up to 5)
   - Print flyer trigger
   ========================================================================= */
(function(){
  "use strict";

  /* Guard: only run on single listing pages */
  if(!document.querySelector(".listing-single-wrap")) return;

  var STORAGE_KEY = "acrelineViewed";
  var MAX_RECENT  = 5;

  /* ============================= RECENTLY VIEWED ============================= */
  function trackView(){
    var idEl    = document.getElementById("listingDetailId");
    var titleEl = document.getElementById("listingDetailTitle");
    var priceEl = document.getElementById("listingDetailPrice");
    var urlEl   = document.getElementById("listingDetailUrl");
    if(!idEl) return;

    var entry = {
      id:    idEl.value,
      title: titleEl ? titleEl.value : document.title,
      price: priceEl ? priceEl.value : "",
      url:   urlEl   ? urlEl.value   : window.location.href,
      ts:    Date.now()
    };

    var list = [];
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if(raw) list = JSON.parse(raw) || [];
    } catch(e){}

    /* Remove existing entry for this page if present */
    list = list.filter(function(i){ return i.id !== entry.id; });
    /* Prepend current */
    list.unshift(entry);
    /* Trim to max */
    list = list.slice(0, MAX_RECENT);

    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(list)); } catch(e){}
  }

  function renderRecentlyViewed(){
    var wrap = document.getElementById("recentlyViewedWrap");
    if(!wrap) return;

    var list = [];
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if(raw) list = JSON.parse(raw) || [];
    } catch(e){}

    /* Exclude current page */
    var idEl = document.getElementById("listingDetailId");
    var currentId = idEl ? idEl.value : null;
    list = list.filter(function(i){ return i.id !== currentId; });

    if(!list.length){
      wrap.hidden = true;
      return;
    }
    wrap.hidden = false;

    var listEl = document.getElementById("recentlyViewedList");
    if(!listEl) return;

    listEl.innerHTML = list.map(function(item){
      var priceStr = item.price ? '<span class="rv-price">$'+Number(item.price).toLocaleString("en-US")+'</span>' : "";
      return (
        '<a class="rv-item" href="'+item.url+'">' +
          priceStr +
          '<span class="rv-title">'+item.title+'</span>' +
        '</a>'
      );
    }).join("");
  }

  trackView();
  renderRecentlyViewed();

  /* ============================= STICKY CTA ============================= */
  var stickyCta  = document.getElementById("listingStickyCta");
  var heroEl     = document.querySelector(".listing-single-hero");
  var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if(stickyCta && heroEl){
    var lastVisible = false;
    function onScroll(){
      var heroBottom = heroEl.getBoundingClientRect().bottom;
      var visible    = heroBottom < 0;
      if(visible !== lastVisible){
        lastVisible = visible;
        stickyCta.classList.toggle("is-visible", visible);
        stickyCta.setAttribute("aria-hidden", visible ? "false" : "true");
      }
    }
    window.addEventListener("scroll", onScroll, { passive:true });
    onScroll();
  }

  /* ============================= SHARE BUTTON ============================= */
  var shareBtn = document.getElementById("listingShareBtn");
  if(shareBtn){
    shareBtn.addEventListener("click", function(){
      var title = document.title;
      var url   = window.location.href;
      if(navigator.share){
        navigator.share({ title:title, url:url }).catch(function(){});
      } else {
        /* Fallback: copy URL */
        if(navigator.clipboard){
          navigator.clipboard.writeText(url).then(function(){
            shareBtn.textContent = "Link copied!";
            setTimeout(function(){ shareBtn.textContent = "Share"; }, 2000);
          });
        }
      }
    });
  }

  /* ============================= PRINT FLYER ============================= */
  var printBtn = document.getElementById("listingPrintBtn");
  if(printBtn){
    printBtn.addEventListener("click", function(){ window.print(); });
  }

})();
