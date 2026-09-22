/* =========================================================================
   Acreline — GLOBAL behaviors (shared on every page)
   - Mobile nav toggle + Escape / focus return
   - Sticky header shadow on scroll
   - Scroll-reveal IntersectionObserver (respects prefers-reduced-motion)
   - Concept chat widget (dialog semantics + aria-live)
   - Current year in footer
   ========================================================================= */
(function(){
  "use strict";

  var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  document.documentElement.classList.add("js-ready");

  /* ============================= MOBILE NAV =============================
     Click / close / trap live in the undelayed script in header.blade.php.
     Do not bind a second click here — that opens then immediately closes. */
  var hamburgerBtn = document.getElementById("hamburgerBtn");
  var mobileNav = document.getElementById("mobileNav");

  function setNavOpen(open){
    if(!hamburgerBtn || !mobileNav) return;
    hamburgerBtn.setAttribute("aria-expanded", open ? "true" : "false");
    hamburgerBtn.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    document.body.classList.toggle("nav-open", open);
    mobileNav.hidden = !open;
    mobileNav.setAttribute("aria-hidden", open ? "false" : "true");
    mobileNav.classList.toggle("is-open", open);
    var navBackdrop = document.getElementById("navBackdrop");
    if(navBackdrop){
      navBackdrop.hidden = !open;
      navBackdrop.classList.toggle("is-open", open);
    }
  }

  /* ============================= HEADER SHADOW ON SCROLL ============================= */
  var header = document.querySelector(".site-header");
  if(header){
    var onScroll = function(){
      header.classList.toggle("scrolled", window.scrollY > 8);
    };
    window.addEventListener("scroll", onScroll, {passive:true});
    onScroll();
  }

  /* ============================= REVEAL ON SCROLL ============================= */
  var revealEls = document.querySelectorAll(".reveal");
  function markInView(el){ el.classList.add("in-view"); }
  if(reduceMotion){
    revealEls.forEach(markInView);
  } else if("IntersectionObserver" in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          markInView(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, {threshold:.12, rootMargin:"0px 0px -40px 0px"});
    revealEls.forEach(function(el){ io.observe(el); });
    window.setTimeout(function(){
      revealEls.forEach(markInView);
    }, 80);
  } else {
    revealEls.forEach(markInView);
  }

  /* ============================= CONCEPT CHAT WIDGET ============================= */
  var chatFab = document.getElementById("chatFab");
  var chatWidget = document.getElementById("chatWidget");
  var chatCloseBtn = document.getElementById("chatCloseBtn");
  var chatBody = document.getElementById("chatBody");
  var chatQuick = document.getElementById("chatQuick");

  var REPLIES = {
    land: {label:"Land prices?", reply:"Sample land parcels in this demo range from about $129,000 to $215,000. Open Listings and filter by Land — numbers are fictional."},
    historic: {label:"Historic homes?", reply:"This concept includes sample historic-style listings. Browse Listings or book a demo showing from the homepage."},
    tour: {label:"Book a tour", reply:"Use the demo showing scheduler on the homepage — pick a sample home, date and time slot. Nothing is emailed; it's a UX demo only."},
    financing: {label:"Financing help?", reply:"Open the Guide page for sample loan and pre-qualification tools, or use the homepage payment and value estimators for quick demos."}
  };

  function openChat(){
    if(!chatWidget || !chatFab) return;
    chatWidget.classList.add("open");
    chatWidget.removeAttribute("hidden");
    chatWidget.setAttribute("aria-hidden","false");
    chatFab.setAttribute("aria-expanded","true");
    document.body.classList.add("chat-open");
    if(chatCloseBtn) chatCloseBtn.focus();
  }

  function closeChat(){
    if(!chatWidget || !chatFab) return;
    chatWidget.classList.remove("open");
    chatWidget.setAttribute("hidden","");
    chatWidget.setAttribute("aria-hidden","true");
    chatFab.setAttribute("aria-expanded","false");
    document.body.classList.remove("chat-open");
    chatFab.focus();
  }

  function appendChat(text, who){
    if(!chatBody) return;
    var div = document.createElement("div");
    div.className = "chat-msg " + who;
    div.textContent = text;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  if(chatFab && chatWidget){
    if(!chatWidget.classList.contains("open")){
      chatWidget.setAttribute("hidden","");
      chatWidget.setAttribute("aria-hidden","true");
    }
    chatFab.addEventListener("click", function(){
      if(chatWidget.hasAttribute("hidden")) openChat();
      else closeChat();
    });
    if(chatCloseBtn) chatCloseBtn.addEventListener("click", closeChat);
    if(chatQuick){
      chatQuick.addEventListener("click", function(e){
        var btn = e.target.closest("button[data-q]");
        if(!btn) return;
        var key = btn.getAttribute("data-q");
        var r = REPLIES[key];
        if(!r) return;
        appendChat(r.label, "user");
        window.setTimeout(function(){ appendChat(r.reply, "bot"); }, reduceMotion ? 0 : 400);
      });
    }
  }

  /* Escape closes chat or mobile nav */
  document.addEventListener("keydown", function(e){
    if(e.key !== "Escape") return;
    if(chatWidget && !chatWidget.hasAttribute("hidden")) closeChat();
    else if(mobileNav && !mobileNav.hasAttribute("hidden")){
      setNavOpen(false);
      if(hamburgerBtn) hamburgerBtn.focus();
    }
  });

  /* ============================= CURRENT YEAR ============================= */
  var yearEls = document.querySelectorAll("[data-year]");
  var yr = new Date().getFullYear();
  yearEls.forEach(function(el){ el.textContent = yr; });

  /* ============================= TOP BAR ============================= */
  (function(){
    var topBar    = document.getElementById("topBar");
    var closeBtn  = document.getElementById("topBarClose");
    var header    = document.querySelector(".site-header");
    var DISMISS_KEY = "ks_top_bar_dismissed";

    if(!topBar) return;

    /* Mark <body> so the sticky header offset CSS var can target it. */
    document.body.classList.add("has-top-bar");

    /* Measure and set the CSS variable used to push the sticky header down. */
    function setTopBarHeight(){
      var h = topBar.offsetHeight;
      document.documentElement.style.setProperty("--top-bar-h", h + "px");
    }

    /* Restore dismissed state from sessionStorage. */
    function restoreDismissed(){
      try {
        if(sessionStorage.getItem(DISMISS_KEY) === "1"){
          topBar.classList.add("is-dismissed");
          document.body.classList.remove("has-top-bar");
          document.documentElement.style.removeProperty("--top-bar-h");
        }
      } catch(e){}
    }

    /* Dismiss the top bar, store preference. */
    function dismiss(){
      topBar.classList.add("is-dismissed");
      document.body.classList.remove("has-top-bar");
      document.documentElement.style.removeProperty("--top-bar-h");
      try { sessionStorage.setItem(DISMISS_KEY, "1"); } catch(e){}
      /* Return focus to header or hamburger button. */
      var focusFallback = document.querySelector(".site-header a, .site-header button");
      if(focusFallback) focusFallback.focus();
    }

    restoreDismissed();

    if(!topBar.classList.contains("is-dismissed")){
      setTopBarHeight();
      window.addEventListener("resize", setTopBarHeight, { passive: true });
    }

    if(closeBtn){
      closeBtn.addEventListener("click", dismiss);
      closeBtn.addEventListener("keydown", function(e){
        if(e.key === "Enter" || e.key === " "){ e.preventDefault(); dismiss(); }
      });
    }
  }());

})();
