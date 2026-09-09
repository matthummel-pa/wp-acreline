/* =========================================================================
   Acreline — LISTINGS page tools
   - Listing data, filters, sort, grid render
   - Grid / map toggle + pins
   - Save hearts (localStorage-persisted) + saved drawer
   - Listing comparison (up to 3) with side-by-side table
   - Detail modal with mortgage estimate
   - Reads ?type=&price=&acreage=&township= from the home search form
   ========================================================================= */
(function(){
  "use strict";

  /* ============================= DATA ============================= */
  var LISTINGS = [
    {
      id:1, type:"historic", typeLabel:"Historic Home", status:"active",
      title:"Ridge Road Brick Farmhouse (c.1890)",
      address:"1755 Ridge Road, North Ridge, PA 00000",
      township:"Cumberland", price:525000,
      beds:4, baths:2, sqft:2400, acres:8.2,
      grad:"linear-gradient(135deg,#155539,#1f6b4a)",
      desc:"A handsome 1890s brick farmhouse with original hardwood floors, a wraparound porch, and a restored bank barn. Eight-plus acres of gently rolling pasture bordered by mature hedgerow, minutes from the county seat.",
      lat:28, lng:22
    },
    {
      id:2, type:"land", typeLabel:"Land / Acreage", status:"active",
      title:"Mill Run Land Parcel",
      address:"62 Mill Run Rd, Mill Creek, PA 00000",
      township:"Straban", price:215000,
      beds:0, baths:0, sqft:0, acres:38,
      grad:"linear-gradient(135deg,#059669,#34d399)",
      desc:"Thirty-eight acres of mostly tillable ground along Mill Run, currently leased for row crops. Road frontage, public water available at the road, and a soil map on file showing strong Class II farmland.",
      lat:52, lng:62
    },
    {
      id:3, type:"farm", typeLabel:"Working Farm", status:"active",
      title:"Wheatland Farmhouse & Outbuildings",
      address:"1420 Orchard Farm Rd, North Ridge, PA 00000",
      township:"Cumberland", price:649000,
      beds:4, baths:2.5, sqft:2850, acres:12.4,
      grad:"linear-gradient(135deg,#1f6b4a,#155539)",
      desc:"A well-kept 1970s farmhouse updated top to bottom, with a 40x60 pole barn, fenced pasture, and a spring-fed pond. Long been run as a small cattle operation; equally suited to a hobby farm or horse property.",
      lat:20, lng:38
    },
    {
      id:4, type:"home", typeLabel:"Home", status:"active",
      title:"Ridge Lane Cottage",
      address:"980 Ridge Lane, North Ridge, PA 00000",
      township:"Cumberland", price:349900,
      beds:3, baths:2, sqft:1680, acres:0.6,
      grad:"linear-gradient(135deg,#3f3f46,#71717a)",
      desc:"A move-in-ready cottage a short walk from the square, with an updated kitchen, screened porch, and a level, fenced back yard. Ideal starter home or in-town pied-a-terre.",
      lat:34, lng:18
    },
    {
      id:5, type:"land", typeLabel:"Land / Acreage", status:"active",
      title:"Creek Bottom Grazing Land",
      address:"215 Creek Bottom Rd, Mill Creek, PA 00000",
      township:"Straban", price:180000,
      beds:0, baths:0, sqft:0, acres:45,
      grad:"linear-gradient(135deg,#047857,#059669)",
      desc:"Forty-five acres of fenced pasture along the creek with a run-in shed and gravity-fed water. Long history as grazing ground for a beef cattle herd; also suitable for hay or a future homesite.",
      lat:58, lng:74
    },
    {
      id:6, type:"farm", typeLabel:"Working Farm", status:"pending",
      title:"Oak Ridge Orchard Farm",
      address:"4110 Orchard Lane, Oak Hollow, PA 00000",
      township:"Franklin", price:875000,
      beds:4, baths:3, sqft:3200, acres:60,
      grad:"linear-gradient(135deg,#d97706,#f59e0b)",
      desc:"A sixty-acre producing apple and peach orchard with a renovated farmhouse, cold storage building, and roadside stand. A rare turn-key opportunity to continue an established orchard operation.",
      lat:70, lng:30
    },
    {
      id:7, type:"historic", typeLabel:"Historic Home", status:"active",
      title:"The Stone Homestead (c.1852)",
      address:"310 Orchard Ridge Rd, Oak Hollow, PA 00000",
      township:"Franklin", price:795000,
      beds:5, baths:3, sqft:3600, acres:22,
      grad:"linear-gradient(135deg,#0c0c0c,#27272a)",
      desc:"A stone-and-frame homestead dating to 1852, lovingly maintained through six generations. Original summer kitchen, restored bank barn, twenty-two acres, and documented 19th-century provenance.",
      lat:78, lng:44
    },
    {
      id:8, type:"land", typeLabel:"Land / Acreage", status:"new",
      title:"Hill View Lot",
      address:"0 Hill View Rd, Oak Hollow, PA 00000",
      township:"Franklin", price:129000,
      beds:0, baths:0, sqft:0, acres:5.5,
      grad:"linear-gradient(135deg,#1f6b4a,#9fd5bb)",
      desc:"A wooded 5.5-acre building lot with long-range ridge views. Perc-approved for a conventional septic system; electric at the road. A quiet, buildable spot minutes from the county seat.",
      lat:86, lng:56
    }
  ];

  if(window.ACRELINE && Array.isArray(window.ACRELINE.listings) && window.ACRELINE.listings.length){
    LISTINGS = window.ACRELINE.listings;
  }

  if(!document.getElementById("listingGrid")) return; /* not the listings page */

  /* ============================= SAVED (localStorage) ============================= */
  var savedListings = {};
  try {
    var _saved = localStorage.getItem("acrelineSaved");
    if(_saved) savedListings = JSON.parse(_saved) || {};
  } catch(e) {}

  function persistSaved(){
    try { localStorage.setItem("acrelineSaved", JSON.stringify(savedListings)); } catch(e) {}
  }

  /* ============================= COMPARE ============================= */
  var compareSet = {};   /* id → true */

  /* ============================= HELPERS ============================= */
  var pinnedId = null;

  var currency = function(n){
    return "$" + Math.round(n).toLocaleString("en-US");
  };

  /* ============================= ICONS ============================= */
  var ICONS = {
    bed:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6"/><path d="M3 18v2M21 18v2"/><path d="M5 12V8a2 2 0 012-2h3v6"/></svg>',
    bath:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4z"/><path d="M4 12V6a2 2 0 012-2 2 2 0 012 2"/><line x1="2" y1="19" x2="22" y2="19"/></svg>',
    sqft:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M9 3v18M3 9h6"/></svg>',
    acres:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4l7 7M4 4h5M4 4v5"/><path d="M20 20l-7-7M20 20h-5M20 20v-5"/><path d="M14 4l-4 4M4 14l4 4"/></svg>'
  };

  var TYPE_COLOR = { home:"#1f6b4a", farm:"#059669", land:"#d97706", historic:"#3f3f46" };
  var TOWNSHIP_LABELS = { Cumberland:"North Ridge", Straban:"Mill Creek", Franklin:"Oak Hollow" };
  function townshipLabel(l){
    return l.townshipLabel || TOWNSHIP_LABELS[l.township] || l.township;
  }

  function specsHTML(l){
    var parts = [];
    if(l.type !== "land"){
      parts.push('<span>'+ICONS.bed+' '+l.beds+' bd</span>');
      parts.push('<span>'+ICONS.bath+' '+l.baths+' ba</span>');
      parts.push('<span>'+ICONS.sqft+' '+Number(l.sqft).toLocaleString()+' sqft</span>');
    }
    parts.push('<span>'+ICONS.acres+' '+l.acres+' ac</span>');
    return parts.join("");
  }

  function statusLabel(s){
    return s === "active" ? "Active" : s === "pending" ? "Pending" : s === "sold" ? "Sold" : "New";
  }

  /* ============================= RENDER LISTINGS ============================= */
  var gridEl   = document.getElementById("listingGrid");
  var emptyEl  = document.getElementById("emptyState");
  var countEl  = document.getElementById("resultCount");
  var pinsEl   = document.getElementById("mapPins");

  function cardTemplate(l){
    var saved   = !!savedListings[l.id];
    var compared = !!compareSet[l.id];
    return (
      '<article class="card" id="card-'+l.id+'" data-id="'+l.id+'">' +
        '<div class="card-photo" style="'+(l.image ? 'background-image:url('+l.image+');background-size:cover;background-position:center;' : 'background:'+l.grad+';')+'">' +
          '<span class="status-tag status-'+l.status+'">'+statusLabel(l.status)+'</span>' +
          '<span class="card-tag">'+l.typeLabel+'</span>' +
          '<button type="button" class="save-heart" aria-label="Save '+l.title+'" aria-pressed="'+saved+'" data-save="'+l.id+'">' +
            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7.5-4.6-10-9.3C.5 8 2.4 4.5 6 4c2.1-.3 4 .8 6 3.1C14 4.8 15.9 3.7 18 4c3.6.5 5.5 4 4 7.7-2.5 4.7-10 9.3-10 9.3z"/></svg>' +
          '</button>' +
        '</div>' +
        '<div class="card-body">' +
          '<span class="card-price">'+currency(l.price)+'</span>' +
          '<h3 class="card-title">'+l.title+'</h3>' +
          '<p class="card-address"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><span>'+l.address+'</span></p>' +
          '<div class="card-specs">'+specsHTML(l)+'</div>' +
          '<div class="card-actions">' +
            '<button type="button" class="btn btn-primary btn-sm" data-view="'+l.id+'">View details</button>' +
            '<button type="button" class="btn btn-ghost btn-sm compare-btn" aria-pressed="'+compared+'" data-compare="'+l.id+'">'+
              (compared ? 'Added ✓' : 'Compare')+
            '</button>' +
          '</div>' +
        '</div>' +
      '</article>'
    );
  }

  function getFilters(prefix){
    var typeEl     = document.getElementById(prefix+"Type");
    var priceEl    = document.getElementById(prefix+"Price");
    var acreageEl  = document.getElementById(prefix+"Acreage");
    var townshipEl = document.getElementById(prefix+"Township");
    var statusEl   = document.getElementById(prefix+"Status");
    return {
      type:     typeEl     ? typeEl.value     : "all",
      price:    priceEl    ? priceEl.value    : "all",
      acreage:  acreageEl  ? acreageEl.value  : "all",
      township: townshipEl ? townshipEl.value : "all",
      status:   statusEl   ? statusEl.value   : "all"
    };
  }

  function applyFilters(f){
    return LISTINGS.filter(function(l){
      if(f.type !== "all" && l.type !== f.type) return false;
      if(f.township !== "all" && l.township !== f.township) return false;
      if(f.status !== "all" && l.status !== f.status) return false;
      if(f.price !== "all"){
        var pr = f.price.split("-").map(Number);
        if(l.price < pr[0] || l.price > pr[1]) return false;
      }
      if(f.acreage !== "all"){
        var ar = f.acreage.split("-").map(Number);
        if(l.acres < ar[0] || l.acres > ar[1]) return false;
      }
      return true;
    });
  }

  function sortListings(list, sortVal){
    var copy = list.slice();
    if(sortVal === "price-asc")    copy.sort(function(a,b){return a.price-b.price;});
    else if(sortVal === "price-desc") copy.sort(function(a,b){return b.price-a.price;});
    else if(sortVal === "acres-desc" || sortVal === "acreage-desc") copy.sort(function(a,b){return b.acres-a.acres;});
    else if(sortVal === "newest")  copy.sort(function(a,b){return b.id-a.id;});
    return copy;
  }

  function renderPins(list){
    if(!pinsEl) return;
    pinsEl.innerHTML = list.map(function(l){
      var color = TYPE_COLOR[l.type] || "#1f6b4a";
      return (
        '<button type="button" class="map-pin" style="left:'+l.lng+'%;top:'+l.lat+'%;" data-pin="'+l.id+'" aria-label="'+l.title+', '+currency(l.price)+'">' +
          '<span class="pin-price">'+currency(l.price)+'</span>' +
          '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="'+color+'" stroke="#fffdf8" stroke-width="1.5" d="M12 2C7.6 2 4 5.6 4 10c0 6 8 12 8 12s8-6 8-12c0-4.4-3.6-8-8-8z"/><circle cx="12" cy="10" r="3" fill="#fffdf8"/></svg>' +
        '</button>'
      );
    }).join("");
  }

  function render(){
    var f       = getFilters("f");
    var sortEl  = document.getElementById("fSort");
    var sortVal = sortEl ? sortEl.value : "price-asc";
    var filtered = sortListings(applyFilters(f), sortVal);

    countEl.innerHTML = "<strong>"+filtered.length+"</strong> " + (filtered.length === 1 ? "property" : "properties") + " found";

    if(filtered.length === 0){
      gridEl.style.display = "none";
      if(emptyEl) { emptyEl.hidden = false; }
    } else {
      gridEl.style.display = "grid";
      if(emptyEl) { emptyEl.hidden = true; }
      gridEl.innerHTML = filtered.map(cardTemplate).join("");
    }
    renderPins(filtered);
    if(pinnedId){
      var card = document.getElementById("card-"+pinnedId);
      if(card) card.classList.add("pinned");
    }
  }

  /* ============================= FILTER EVENTS ============================= */
  ["fType","fPrice","fAcreage","fTownship","fStatus","fSort"].forEach(function(id){
    var el = document.getElementById(id);
    if(el) el.addEventListener("change", render);
  });

  var filterForm = document.getElementById("filterForm");
  if(filterForm) filterForm.addEventListener("submit", function(e){ e.preventDefault(); });

  var resetBtn = document.getElementById("resetFilters");
  if(resetBtn) resetBtn.addEventListener("click", resetFilters);
  var emptyResetBtn = document.getElementById("emptyResetBtn");
  if(emptyResetBtn) emptyResetBtn.addEventListener("click", resetFilters);

  function resetFilters(){
    ["fType","fPrice","fAcreage","fTownship","fStatus","fSort"].forEach(function(id){
      var el = document.getElementById(id);
      if(el) el.selectedIndex = 0;
    });
    render();
  }

  /* ============================= URL PARAMS ============================= */
  function applyUrlParams(){
    var params = new URLSearchParams(window.location.search);
    var map = { type:"fType", price:"fPrice", acreage:"fAcreage", township:"fTownship" };
    Object.keys(map).forEach(function(param){
      var val = params.get(param);
      if(val){
        var sel = document.getElementById(map[param]);
        if(!sel) return;
        var ok = Array.prototype.some.call(sel.options, function(o){ return o.value === val; });
        if(ok) sel.value = val;
      }
    });
  }
  applyUrlParams();

  /* ============================= GRID CLICK DELEGATION ============================= */
  gridEl.addEventListener("click", function(e){
    var viewBtn    = e.target.closest("[data-view]");
    var saveBtn    = e.target.closest("[data-save]");
    var compareBtn = e.target.closest("[data-compare]");
    if(viewBtn){
      openModal(Number(viewBtn.getAttribute("data-view")));
    } else if(saveBtn){
      toggleSave(saveBtn);
    } else if(compareBtn){
      toggleCompare(compareBtn);
    }
  });

  /* ============================= SAVE HEARTS ============================= */
  function toggleSave(btn){
    var id = Number(btn.getAttribute("data-save"));
    if(savedListings[id]){
      delete savedListings[id];
    } else {
      savedListings[id] = true;
    }
    btn.setAttribute("aria-pressed", !!savedListings[id]);
    persistSaved();
    updateSavedFab();
    updateSavedDrawer();
    /* sync modal save button if open for same listing */
    var overlay = document.getElementById("modalOverlay");
    if(overlay && overlay.dataset.listingId === String(id)){
      var saveModalBtn = document.getElementById("modalSaveBtn");
      if(saveModalBtn) saveModalBtn.textContent = savedListings[id] ? "Saved ✓" : "Save Listing";
    }
  }

  /* ============================= SAVED FAB + DRAWER ============================= */
  var savedFab   = document.getElementById("savedFab");
  var savedDrawer = document.getElementById("savedDrawer");

  function updateSavedFab(){
    if(!savedFab) return;
    var count = Object.keys(savedListings).length;
    savedFab.hidden = count === 0;
    var countEl2 = document.getElementById("savedFabCount");
    if(countEl2) countEl2.textContent = count > 0 ? String(count) : "";
  }

  function updateSavedDrawer(){
    if(!savedDrawer) return;
    var ids      = Object.keys(savedListings).map(Number);
    var countBadge = document.getElementById("savedDrawerCount");
    if(countBadge) countBadge.textContent = ids.length > 0 ? "("+ids.length+")" : "";

    var listEl  = document.getElementById("savedDrawerList");
    var emptyMsg = document.getElementById("savedDrawerEmpty");
    if(!listEl) return;

    if(ids.length === 0){
      listEl.innerHTML = "";
      if(emptyMsg) emptyMsg.hidden = false;
      return;
    }
    if(emptyMsg) emptyMsg.hidden = true;

    var items = ids.map(function(id){
      return LISTINGS.filter(function(l){ return l.id === id; })[0];
    }).filter(Boolean);

    listEl.innerHTML = items.map(function(l){
      return (
        '<div class="saved-drawer-item">' +
          '<div class="saved-item-photo" style="'+(l.image ? 'background-image:url('+l.image+');background-size:cover;background-position:center' : 'background:'+l.grad)+';"></div>' +
          '<div class="saved-item-body">' +
            '<p class="saved-item-price">'+currency(l.price)+'</p>' +
            '<p class="saved-item-title">'+l.title+'</p>' +
            '<p class="saved-item-addr">'+l.address+'</p>' +
          '</div>' +
          '<div class="saved-item-actions">' +
            '<button type="button" class="btn btn-primary btn-sm" data-view="'+l.id+'">View</button>' +
            '<button type="button" class="btn btn-ghost btn-sm" data-unsave="'+l.id+'" aria-label="Remove '+l.title+' from saved">✕</button>' +
          '</div>' +
        '</div>'
      );
    }).join("");

    /* unsave buttons inside drawer */
    listEl.querySelectorAll("[data-unsave]").forEach(function(btn){
      btn.addEventListener("click", function(){
        var id = Number(this.getAttribute("data-unsave"));
        delete savedListings[id];
        persistSaved();
        updateSavedFab();
        updateSavedDrawer();
        /* update heart on card */
        var heart = document.querySelector('.save-heart[data-save="'+id+'"]');
        if(heart) heart.setAttribute("aria-pressed","false");
      });
    });

    /* view buttons inside drawer */
    listEl.querySelectorAll("[data-view]").forEach(function(btn){
      btn.addEventListener("click", function(){
        openModal(Number(this.getAttribute("data-view")));
      });
    });
  }

  if(savedFab){
    savedFab.addEventListener("click", function(){
      if(!savedDrawer) return;
      savedDrawer.hidden = false;
      document.body.classList.add("drawer-open");
      var closeBtn = document.getElementById("savedDrawerClose");
      if(closeBtn) closeBtn.focus();
    });
  }

  var savedDrawerCloseBtn = document.getElementById("savedDrawerClose");
  if(savedDrawerCloseBtn){
    savedDrawerCloseBtn.addEventListener("click", function(){
      if(!savedDrawer) return;
      savedDrawer.hidden = true;
      document.body.classList.remove("drawer-open");
      if(savedFab) savedFab.focus();
    });
  }

  /* ============================= COMPARE ============================= */
  var compareBar       = document.getElementById("compareBar");
  var compareOpenBtn   = document.getElementById("compareOpenBtn");
  var compareClearBtn  = document.getElementById("compareClearBtn");
  var compareModal     = document.getElementById("compareModal");
  var compareCloseBtn  = document.getElementById("compareCloseBtn");
  var compareTable     = document.getElementById("compareTable");
  var compareBarLabel  = document.getElementById("compareBarLabel");

  function toggleCompare(btn){
    var id = Number(btn.getAttribute("data-compare"));
    if(compareSet[id]){
      delete compareSet[id];
    } else {
      var count = Object.keys(compareSet).length;
      if(count >= 3){
        /* silently ignore if already at max */
        return;
      }
      compareSet[id] = true;
    }
    updateCompareBar();
    /* update button text */
    var pressed = !!compareSet[id];
    btn.setAttribute("aria-pressed", pressed);
    btn.textContent = pressed ? "Added ✓" : "Compare";
  }

  function updateCompareBar(){
    if(!compareBar) return;
    var ids   = Object.keys(compareSet);
    var count = ids.length;
    if(compareBarLabel) compareBarLabel.textContent = count + " selected";
    compareBar.hidden = count === 0;
    if(compareOpenBtn){
      compareOpenBtn.disabled = count < 2;
    }
    /* sync all compare buttons in grid */
    gridEl.querySelectorAll(".compare-btn").forEach(function(btn){
      var id = Number(btn.getAttribute("data-compare"));
      var pressed = !!compareSet[id];
      btn.setAttribute("aria-pressed", pressed);
      btn.textContent = pressed ? "Added ✓" : "Compare";
    });
  }

  if(compareOpenBtn){
    compareOpenBtn.addEventListener("click", openCompareModal);
  }
  if(compareClearBtn){
    compareClearBtn.addEventListener("click", function(){
      compareSet = {};
      updateCompareBar();
      compareBar.hidden = true;
    });
  }
  if(compareCloseBtn){
    compareCloseBtn.addEventListener("click", function(){
      if(compareModal) compareModal.hidden = true;
      document.body.style.overflow = "";
      if(compareOpenBtn) compareOpenBtn.focus();
    });
  }
  if(compareModal){
    compareModal.addEventListener("click", function(e){
      if(e.target === compareModal){
        compareModal.hidden = true;
        document.body.style.overflow = "";
      }
    });
  }

  function openCompareModal(){
    var ids   = Object.keys(compareSet).map(Number);
    var items = ids.map(function(id){
      return LISTINGS.filter(function(l){ return l.id === id; })[0];
    }).filter(Boolean);

    if(items.length < 2 || !compareTable) return;

    var rows = [
      { label:"Photo",    fn: function(l){ return '<div class="ct-photo" style="'+(l.image?'background-image:url('+l.image+');background-size:cover;background-position:center':'background:'+l.grad)+';"></div>'; } },
      { label:"Price",    fn: function(l){ return '<strong>'+currency(l.price)+'</strong>'; } },
      { label:"Status",   fn: function(l){ return '<span class="status-tag status-'+l.status+'">'+statusLabel(l.status)+'</span>'; } },
      { label:"Type",     fn: function(l){ return l.typeLabel; } },
      { label:"Area",     fn: function(l){ return townshipLabel(l); } },
      { label:"Beds",     fn: function(l){ return l.type !== "land" ? String(l.beds) : '—'; } },
      { label:"Baths",    fn: function(l){ return l.type !== "land" ? String(l.baths) : '—'; } },
      { label:"Sq Ft",    fn: function(l){ return l.sqft ? Number(l.sqft).toLocaleString() : '—'; } },
      { label:"Acres",    fn: function(l){ return l.acres ? l.acres+' ac' : '—'; } },
      { label:"Year",     fn: function(l){ return l.year_built || '—'; } },
      { label:"$/sqft",   fn: function(l){ return (l.sqft && l.sqft > 0) ? '$'+Math.round(l.price/l.sqft) : '—'; } },
      { label:"Address",  fn: function(l){ return l.address; } }
    ];

    var headerCols = '<th scope="col"></th>' + items.map(function(l){
      return '<th scope="col">'+l.title+'</th>';
    }).join("");

    var bodyRows = rows.map(function(row){
      return '<tr><th scope="row">'+row.label+'</th>' + items.map(function(l){
        return '<td>'+row.fn(l)+'</td>';
      }).join("") + '</tr>';
    }).join("");

    var actionsRow = '<tr class="ct-actions-row"><th scope="row"></th>' + items.map(function(l){
      var book = (window.ACRELINE && window.ACRELINE.bookUrl) || "/book/";
      return '<td><a class="btn btn-primary btn-sm" href="'+book+'?listing_id='+l.id+'">Book showing</a></td>';
    }).join("") + '</tr>';

    compareTable.innerHTML = (
      '<table class="compare-table">' +
        '<thead><tr>'+headerCols+'</tr></thead>' +
        '<tbody>'+bodyRows+actionsRow+'</tbody>' +
      '</table>'
    );

    if(compareModal){
      compareModal.hidden = false;
      document.body.style.overflow = "hidden";
      if(compareCloseBtn) compareCloseBtn.focus();
    }
  }

  /* ============================= GRID / MAP TOGGLE ============================= */
  var gridBtn = document.getElementById("gridViewBtn");
  var mapBtn  = document.getElementById("mapViewBtn");
  var mapView = document.getElementById("mapView");

  if(gridBtn) gridBtn.addEventListener("click", function(){
    gridBtn.classList.add("active"); gridBtn.setAttribute("aria-pressed","true");
    if(mapBtn){ mapBtn.classList.remove("active"); mapBtn.setAttribute("aria-pressed","false"); }
    if(mapView) mapView.hidden = true;
    gridEl.style.display = LISTINGS.length ? "grid" : "none";
  });
  if(mapBtn) mapBtn.addEventListener("click", function(){
    mapBtn.classList.add("active"); mapBtn.setAttribute("aria-pressed","true");
    if(gridBtn){ gridBtn.classList.remove("active"); gridBtn.setAttribute("aria-pressed","false"); }
    if(mapView) mapView.hidden = false;
    gridEl.style.display = "none";
  });

  if(pinsEl) pinsEl.addEventListener("click", function(e){
    var pin = e.target.closest("[data-pin]");
    if(!pin) return;
    var id = Number(pin.getAttribute("data-pin"));
    pinnedId = id;
    document.querySelectorAll(".map-pin").forEach(function(p){p.classList.remove("pin-active");});
    pin.classList.add("pin-active");
    if(gridBtn) gridBtn.click();
    requestAnimationFrame(function(){
      var card = document.getElementById("card-"+id);
      if(card){
        card.scrollIntoView({behavior:"smooth", block:"center"});
        document.querySelectorAll(".card").forEach(function(c){c.classList.remove("pinned");});
        card.classList.add("pinned");
      }
    });
  });

  /* ============================= MODAL ============================= */
  var overlay     = document.getElementById("modalOverlay");
  var lastFocused = null;

  function galleryTiles(l){
    var shades = [l.grad,
      "linear-gradient(135deg,#dbeafe,#1f6b4a)",
      "linear-gradient(135deg,#a1a1aa,#3f3f46)",
      "linear-gradient(135deg,#bfdbfe,#155539)"];
    return (
      '<div class="modal-gallery-main" style="background:'+shades[0]+';"></div>' +
      '<div style="background:'+shades[1]+';"></div>' +
      '<div style="background:'+shades[2]+';"></div>'
    );
  }

  function openModal(id){
    var l = LISTINGS.filter(function(x){ return x.id === id; })[0];
    if(!l || !overlay) return;
    lastFocused = document.activeElement;

    document.getElementById("modalGallery").innerHTML = galleryTiles(l);
    document.getElementById("modalTag").textContent   = l.typeLabel + " · " + townshipLabel(l);
    document.getElementById("modalTitle").textContent  = l.title;
    document.getElementById("modalAddress").querySelector("span").textContent = l.address;
    document.getElementById("modalPrice").textContent  = currency(l.price);

    var statusEl = document.getElementById("modalStatus");
    statusEl.textContent = statusLabel(l.status);
    statusEl.className   = "status-tag status-"+l.status;

    document.getElementById("modalDesc").textContent = l.desc;

    var specs = [];
    if(l.type !== "land"){
      specs.push({v:l.beds,            k:"Beds"});
      specs.push({v:l.baths,           k:"Baths"});
      specs.push({v:l.sqft.toLocaleString(), k:"Sq Ft"});
    }
    specs.push({v:l.acres, k:"Acres"});
    document.getElementById("modalSpecs").innerHTML = specs.map(function(s){
      return '<div><strong>'+s.v+'</strong><span>'+s.k+'</span></div>';
    }).join("");

    document.getElementById("calcPrice").value = l.price;
    overlay.dataset.listingId = String(l.id);

    var book = (window.ACRELINE && window.ACRELINE.bookUrl) || "/book/";
    document.getElementById("modalScheduleBtn").setAttribute("href", book + (book.indexOf("?") >= 0 ? "&" : "?") + "listing_id=" + l.id);

    var saveModalBtn = document.getElementById("modalSaveBtn");
    if(saveModalBtn) saveModalBtn.textContent = savedListings[l.id] ? "Saved ✓" : "Save Listing";

    recalcMortgage();

    overlay.hidden = false;
    overlay.classList.add("open");
    document.body.style.overflow = "hidden";
    document.getElementById("modalCloseBtn").focus();
  }

  function closeModal(){
    if(!overlay) return;
    overlay.classList.remove("open");
    overlay.hidden = true;
    document.body.style.overflow = "";
    if(lastFocused) lastFocused.focus();
  }

  if(overlay){
    var closeBtn = document.getElementById("modalCloseBtn");
    if(closeBtn) closeBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", function(e){ if(e.target === overlay) closeModal(); });
  }
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
      if(overlay && overlay.classList.contains("open")) { closeModal(); return; }
      if(compareModal && !compareModal.hidden)          { compareModal.hidden = true; document.body.style.overflow = ""; return; }
      if(savedDrawer && !savedDrawer.hidden)            { savedDrawer.hidden = true; document.body.classList.remove("drawer-open"); }
    }
  });

  var scheduleBtn = document.getElementById("modalScheduleBtn");
  if(scheduleBtn){
    scheduleBtn.addEventListener("click", function(){
      if(pinnedId || (overlay && overlay.dataset.listingId)){
        var id   = (overlay && overlay.dataset.listingId) || pinnedId;
        var book = (window.ACRELINE && window.ACRELINE.bookUrl) || "/book/";
        this.setAttribute("href", book + (book.indexOf("?") >= 0 ? "&" : "?") + "listing_id=" + id);
      }
      closeModal();
    });
  }

  var saveModalBtn = document.getElementById("modalSaveBtn");
  if(saveModalBtn){
    saveModalBtn.addEventListener("click", function(){
      var id = overlay ? Number(overlay.dataset.listingId) : 0;
      if(!id) return;
      if(savedListings[id]){
        delete savedListings[id];
        this.textContent = "Save Listing";
      } else {
        savedListings[id] = true;
        this.textContent = "Saved ✓";
      }
      persistSaved();
      updateSavedFab();
      updateSavedDrawer();
      /* sync heart on card */
      var heart = document.querySelector('.save-heart[data-save="'+id+'"]');
      if(heart) heart.setAttribute("aria-pressed", !!savedListings[id]);
    });
  }

  /* ============================= MORTGAGE CALCULATOR ============================= */
  function recalcMortgage(){
    var price = Number(document.getElementById("calcPrice").value) || 0;
    var downPct = Number(document.getElementById("calcDown").value) || 0;
    var rate    = Number(document.getElementById("calcRate").value) || 0;
    var years   = Number(document.getElementById("calcTerm").value) || 30;

    var loan        = price * (1 - downPct/100);
    var monthlyRate = (rate/100)/12;
    var n           = years*12;
    var payment     = 0;
    if(loan > 0){
      if(monthlyRate === 0){
        payment = loan/n;
      } else {
        payment = loan * (monthlyRate * Math.pow(1+monthlyRate, n)) / (Math.pow(1+monthlyRate, n) - 1);
      }
    }
    var el = document.getElementById("calcMonthly");
    if(el) el.textContent = currency(payment) + " /mo";
  }
  ["calcPrice","calcDown","calcRate","calcTerm"].forEach(function(id){
    var el = document.getElementById(id);
    if(el){
      el.addEventListener("input", recalcMortgage);
      el.addEventListener("change", recalcMortgage);
    }
  });

  /* ============================= INIT ============================= */
  render();
  updateSavedFab();
  updateSavedDrawer();

})();
