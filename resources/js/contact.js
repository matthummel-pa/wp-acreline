/* =========================================================================
   Contact page — message confirm + demo land/farm valuation
   Concept only: nothing leaves the browser.
   ========================================================================= */
(function(){
  "use strict";

  var currency = function(n){ return "$" + Math.round(n).toLocaleString("en-US"); };

  var contactForm = document.getElementById("contactForm");
  var contactConfirm = document.getElementById("contactConfirm");
  if(contactForm && contactConfirm){
    contactForm.addEventListener("submit", function(e){
      e.preventDefault();
      var btn = contactForm.querySelector('button[type="submit"]');
      if(btn){
        btn.disabled = true;
        btn.setAttribute("aria-disabled", "true");
        btn.textContent = "Sending…";
      }
      contactConfirm.classList.add("show");
      contactForm.reset();
      if(btn){
        window.setTimeout(function(){
          btn.disabled = false;
          btn.removeAttribute("aria-disabled");
          btn.textContent = "Send Message";
        }, 600);
      }
    });
  }

  var valForm = document.getElementById("valForm");
  var valResult = document.getElementById("valResult");
  if(valForm && valResult){
    valForm.addEventListener("submit", function(e){
      e.preventDefault();
      var type = (document.getElementById("valType") || {}).value || "home";
      var acres = Number((document.getElementById("valAcres") || {}).value) || 0;
      var sqft = Number((document.getElementById("valSqft") || {}).value) || 0;
      var mid = (type === "land" || type === "farm")
        ? 120000 + acres * (type === "farm" ? 11000 : 8500)
        : 160000 + sqft * (type === "historic" ? 95 : 80) + acres * 6500;
      var low = Math.round(mid * 0.9 / 1000) * 1000;
      var high = Math.round(mid * 1.1 / 1000) * 1000;
      var amount = document.getElementById("valResultAmount");
      if(amount) amount.textContent = currency(low) + " – " + currency(high);
      valResult.classList.add("show");
    });
  }
})();
