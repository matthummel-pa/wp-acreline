/* =========================================================================
   Home-only behaviors (also runs sitewide via app.js for shared forms)
   - Search → listings
   - Home value + listing alerts (demo)
   - Showing appointment booking (demo)
   - Contact + contact valuation (demo)
   ========================================================================= */
(function(){
  "use strict";

  var WARN_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>';

  function setBusy(btn, busy, busyLabel){
    if(!btn) return;
    if(busy){
      if(!btn.dataset.label) btn.dataset.label = btn.textContent;
      btn.disabled = true;
      btn.setAttribute("aria-disabled", "true");
      btn.textContent = busyLabel || "Sending…";
    } else {
      btn.disabled = false;
      btn.removeAttribute("aria-disabled");
      if(btn.dataset.label) btn.textContent = btn.dataset.label;
    }
  }

  function showStatus(el, message, isError){
    if(!el) return;
    el.className = "confirm-msg show";
    if(isError){
      el.style.background = "#fff7ed";
      el.style.borderColor = "#fdba74";
      el.style.color = "#9a3412";
      el.innerHTML = WARN_ICON + "<span>" + message + "</span>";
    } else {
      el.style.background = "";
      el.style.borderColor = "";
      el.style.color = "";
      el.innerHTML = "<span>" + message + "</span>";
    }
  }

  var form = document.getElementById("heroSearchForm");
  if(form){
    form.addEventListener("submit", function(e){
      e.preventDefault();
      var params = new URLSearchParams();
      var map = {hsType:"type", hsPrice:"price", hsAcreage:"acreage", hsTownship:"township"};
      Object.keys(map).forEach(function(id){
        var el = document.getElementById(id);
        if(!el) return;
        var v = el.value;
        if(v && v !== "all") params.set(map[id], v);
      });
      var qs = params.toString();
      var listingsUrl = (window.ACRELINE && window.ACRELINE.listingsUrl) || "/listings";
      window.location.href = listingsUrl + (qs ? "?" + qs : "");
    });
  }

  function formatMoney(n){
    return "$" + Math.round(n).toLocaleString("en-US");
  }

  /* Home value demo */
  var valueForm = document.getElementById("valueForm");
  var valueResult = document.getElementById("valueResult");
  if(valueForm && valueResult){
    valueForm.addEventListener("submit", function(e){
      e.preventDefault();
      var btn = valueForm.querySelector("button[type=submit]");
      setBusy(btn, true, "Estimating…");
      var beds = Number(document.getElementById("vBeds").value) || 3;
      var acres = Number(document.getElementById("vAcres").value) || 5;
      var mid = 180000 + beds * 42000 + acres * 8500;
      var low = Math.round(mid * 0.92 / 1000) * 1000;
      var high = Math.round(mid * 1.08 / 1000) * 1000;
      valueResult.className = "val-result show";
      valueResult.innerHTML =
        "<strong>" + formatMoney(low) + " – " + formatMoney(high) + "</strong>" +
        "<span style=\"color:var(--ink-soft);font-size:.9rem\">Demo range for " +
        (document.getElementById("vAddress").value || "your address") +
        ". Not an appraisal.</span>";
      setBusy(btn, false);
    });
  }

  /* Listing alert demo */
  var alertForm = document.getElementById("alertForm");
  var alertConfirm = document.getElementById("alertConfirm");
  if(alertForm && alertConfirm){
    alertForm.addEventListener("submit", function(e){
      e.preventDefault();
      var btn = alertForm.querySelector("button[type=submit]");
      setBusy(btn, true);
      alertConfirm.classList.add("show");
      if(btn){
        btn.textContent = "Saved";
        btn.disabled = true;
        btn.setAttribute("aria-disabled", "true");
      }
    });
  }

  /* Contact message (concept demo — no network) */
  var contactForm = document.getElementById("contactForm");
  var contactConfirm = document.getElementById("contactConfirm");
  if(contactForm && contactConfirm){
    contactForm.addEventListener("submit", function(e){
      e.preventDefault();
      var btn = contactForm.querySelector("button[type=submit]");
      setBusy(btn, true);
      contactConfirm.classList.add("show");
      if(btn){
        btn.textContent = "Sent";
        btn.disabled = true;
        btn.setAttribute("aria-disabled", "true");
      }
    });
  }

  /* Contact page valuation */
  var valForm = document.getElementById("valForm");
  var valResult = document.getElementById("valResult");
  var valResultAmount = document.getElementById("valResultAmount");
  if(valForm && valResult && valResultAmount){
    valForm.addEventListener("submit", function(e){
      e.preventDefault();
      var btn = valForm.querySelector("button[type=submit]");
      setBusy(btn, true, "Estimating…");
      var acres = Number(document.getElementById("valAcres").value) || 0;
      var sqft = Number(document.getElementById("valSqft").value) || 0;
      var type = (document.getElementById("valType") || {}).value || "home";
      var base = type === "land" ? 12000 : (type === "farm" ? 18000 : 22000);
      var mid = acres * base + sqft * 145;
      var low = Math.round(mid * 0.9 / 1000) * 1000;
      var high = Math.round(mid * 1.12 / 1000) * 1000;
      valResultAmount.textContent = formatMoney(low) + " – " + formatMoney(high);
      valResult.classList.add("show");
      valResult.style.display = "block";
      setBusy(btn, false);
    });
  }

  /* Showing booking */
  var showingForm = document.getElementById("showingForm");
  var slotGrid = document.getElementById("slotGrid");
  var showTime = document.getElementById("showTime");
  var showProperty = document.getElementById("showProperty");
  var showingConfirm = document.getElementById("showingConfirm");
  var showDate = document.getElementById("showDate");

  if(showDate){
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    showDate.min = tomorrow.toISOString().slice(0, 10);
    if(!showDate.value) showDate.value = showDate.min;
  }

  if(slotGrid && showTime){
    slotGrid.addEventListener("click", function(e){
      var btn = e.target.closest(".slot");
      if(!btn || btn.disabled) return;
      slotGrid.querySelectorAll(".slot").forEach(function(s){
        s.classList.remove("is-selected");
        s.setAttribute("aria-pressed", "false");
      });
      btn.classList.add("is-selected");
      btn.setAttribute("aria-pressed", "true");
      showTime.value = btn.getAttribute("data-time") || "";
    });
  }

  if(showProperty){
    var params = new URLSearchParams(window.location.search);
    var listingId = params.get("listing_id") || params.get("listing");
    if(listingId){
      showProperty.value = listingId;
    }
  }

  if(showingForm && showingConfirm){
    showingForm.addEventListener("submit", function(e){
      e.preventDefault();
      if(!showTime.value){
        showStatus(showingConfirm, "Pick a time slot to continue.", true);
        return;
      }
      var listingId = showProperty ? showProperty.value : "";
      var propLabel = showProperty && showProperty.selectedOptions[0] ? showProperty.selectedOptions[0].text : "a sample home";
      var when = (showDate && showDate.value ? showDate.value : "your date") + " · " + showTime.value;
      var name = (document.getElementById("showName") || {}).value || "Guest";
      var submitBtn = showingForm.querySelector("button[type=submit]");
      setBusy(submitBtn, true);

      var endpoint = window.ACRELINE && window.ACRELINE.restUrl ? window.ACRELINE.restUrl + "bookings" : "";
      var payload = {
        listing_id: listingId,
        date: showDate ? showDate.value : "",
        time: showTime.value,
        type: (document.getElementById("showType") || {}).value || "in-person",
        name: name,
        phone: (document.getElementById("showPhone") || {}).value || "",
        email: (document.getElementById("showEmail") || {}).value || "",
        notes: (document.getElementById("showNotes") || {}).value || ""
      };

      function showOk(message){
        showingConfirm.className = "confirm-msg show";
        showingConfirm.style.background = "";
        showingConfirm.style.borderColor = "";
        showingConfirm.style.color = "";
        var text = message || (name + " — " + propLabel + " on " + when);
        showingConfirm.innerHTML = "<span>" + text + "</span>";
        if(submitBtn){
          submitBtn.disabled = true;
          submitBtn.setAttribute("aria-disabled", "true");
          submitBtn.textContent = "Requested";
        }
      }

      function showErr(message){
        setBusy(submitBtn, false);
        showStatus(showingConfirm, message || "Could not save this showing. Try again.", true);
      }

      if(!endpoint){
        showOk(name + " — " + propLabel + " on " + when + ". Saved locally only.");
        return;
      }

      fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": (window.ACRELINE && window.ACRELINE.nonce) || ""
        },
        body: JSON.stringify(payload)
      }).then(function(res){
        return res.json().then(function(data){ return { ok: res.ok, data: data }; });
      }).then(function(result){
        if(result.ok){
          showOk(result.data && result.data.message ? result.data.message : (name + " — " + propLabel + " on " + when));
        } else {
          var msg = result.data && result.data.message ? result.data.message : "Could not save this showing.";
          if(Array.isArray(msg)) msg = msg.join(" ");
          showErr(msg);
        }
      }).catch(function(){
        showErr("Network error — the showing was not saved.");
      });
    });
  }
})();
