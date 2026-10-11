import "./bootstrap";
import Alpine from "alpinejs";

window.Alpine = Alpine;

// ✅ Auth Modal Store (tanpa x-modal Breeze)
Alpine.store("authModal", {
  isOpen: false,
  tab: "login",

  open(tab = "login") {
    this.tab = tab;
    this.isOpen = true;
    document.body.classList.add("overflow-hidden"); // biar background ga scroll
  },

  close() {
    this.isOpen = false;
    document.body.classList.remove("overflow-hidden");
  },

  switch(tab) {
    this.tab = tab;
  },
});

// ✅ Review Modal Store (konsisten dengan authModal)
Alpine.store("reviewModal", {
  isOpen: false,
  rating: 0,
  hover: 0,
  error: "",

  open(defaultRating = 5) {
    this.isOpen = true;
    this.error = "";
    this.hover = 0;
    if (!this.rating) this.rating = defaultRating;

    document.body.classList.add("overflow-hidden");
  },

  close() {
    this.isOpen = false;
    this.error = "";
    this.hover = 0;

    document.body.classList.remove("overflow-hidden");
  },

  setHover(v) {
    this.hover = v;
  },

  setRating(v) {
    this.rating = v;
    this.error = "";
  },

  // validasi & submit form
  submit(formEl) {
    if (!this.rating || this.rating < 1) {
      this.error = "Pilih rating terlebih dahulu.";
      return;
    }

    const comment = formEl.querySelector('textarea[name="comment"]');
    const agree = formEl.querySelector('input[name="agree"]');

    if (!comment || !comment.value.trim()) {
      this.error = "Review tidak boleh kosong.";
      return;
    }

    if (!agree || !agree.checked) {
      this.error = "Centang persetujuan terms & conditions.";
      return;
    }

    this.error = "";
    formEl.submit();
  },
});


Alpine.start();

/**
 * NAVBAR: topbar hide saat scroll down, lowerbar naik ke top.
 */
function initNavbarScroll() {
  const topbar = document.getElementById("topbar");
  const lowerbar = document.getElementById("lowerbar");
  if (!topbar || !lowerbar) return;

  let lastY = window.scrollY;
  let ticking = false;

  const applyNavbar = () => {
    const y = window.scrollY;
    const goingDown = y > lastY;
    const past = y > 10;

    if (goingDown && past) {
      topbar.classList.add("-translate-y-full");
      lowerbar.classList.remove("translate-y-16");
      lowerbar.classList.add("translate-y-0");
    } else {
      topbar.classList.remove("-translate-y-full");
      lowerbar.classList.remove("translate-y-0");
      lowerbar.classList.add("translate-y-16");
    }

    lastY = y;
  };

  const onScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        applyNavbar();
        ticking = false;
      });
      ticking = true;
    }
  };

  window.addEventListener("scroll", onScroll, { passive: true });
  applyNavbar();
}

/**
 * CAROUSEL: swipe + dot click + looping
 *
 * IMPORTANT:
 * - Jangan init carousel yang ada di template hidden ([data-price-template])
 * - Tanda "data-inited" mencegah double event
 */
window.initCarousels = function initCarousels() {
  const carousels = document.querySelectorAll("[data-carousel]");

  carousels.forEach((carousel) => {
    // ✅ Jangan init carousel yang berada di template filter harga (hidden)
    if (carousel.closest("[data-price-template], [data-year-template]")) return;

    // ✅ Hindari double init
    if (carousel.dataset.inited === "1") return;
    carousel.dataset.inited = "1";

    const track = carousel.querySelector("[data-carousel-track]");
    const viewport = carousel.querySelector("[data-carousel-viewport]");
    const dotsWrap = carousel.querySelector("[data-carousel-dots]");
    if (!track || !viewport) return;

    const slides = track.children;
    const dots = dotsWrap ? dotsWrap.querySelectorAll("[data-dot]") : [];
    let index = parseInt(track.dataset.index || "0", 10);

    const setIndex = (i) => {
      if (slides.length <= 1) {
        index = 0;
      } else if (i < 0) {
        index = slides.length - 1;
      } else if (i >= slides.length) {
        index = 0;
      } else {
        index = i;
      }

      track.dataset.index = String(index);
      track.style.transform = `translateX(-${index * 100}%)`;

      dots.forEach((d, di) => {
        d.classList.toggle("bg-white", di === index);
        d.classList.toggle("bg-white/50", di !== index);
      });
    };

    // dots click
    dots.forEach((d) => {
      d.addEventListener("click", () => setIndex(parseInt(d.dataset.dot, 10)));
    });

    // prevent image drag ghost
    viewport.querySelectorAll("img").forEach((img) => {
      img.setAttribute("draggable", "false");
    });

    // swipe (touch + mouse)
    let startX = 0;
    let isDragging = false;

    const getX = (e) => (e.touches ? e.touches[0].clientX : e.clientX);

    const onStart = (e) => {
      isDragging = true;
      startX = getX(e);
    };

    const onEnd = (e) => {
      if (!isDragging) return;
      isDragging = false;

      const endX = getX(e.changedTouches ? e.changedTouches[0] : e);
      const deltaX = endX - startX;

      if (Math.abs(deltaX) > 40) {
        if (deltaX < 0) setIndex(index + 1);
        else setIndex(index - 1);
      }
    };

    viewport.addEventListener("touchstart", onStart, { passive: true });
    viewport.addEventListener("touchend", onEnd, { passive: true });

    viewport.addEventListener("mousedown", onStart);
    viewport.addEventListener("mouseup", onEnd);
    viewport.addEventListener("mouseleave", () => (isDragging = false));

    setIndex(index);
  });
};

/**
 * PRICE FILTER (NO REFRESH)
 * - swap cards berdasarkan template
 * - reset data-inited pada carousel hasil swap
 * - init ulang carousel
 */
function initPriceFilterNoRefresh() {
  const section = document.querySelector("[data-price-section]");
  if (!section) return;

  const container = section.querySelector("#price-cards");
  const tabs = section.querySelectorAll("[data-price-tab]");

  const render = (key) => {
    const tpl = section.querySelector(`[data-price-template="${key}"]`);
    if (!tpl) return;

    // swap HTML
    container.innerHTML = tpl.innerHTML;

    // ✅ RESET flag "inited" pada carousel hasil swap
    // (karena template hidden bisa pernah ke-init di DOM lain)
    container.querySelectorAll("[data-carousel]").forEach((c) => {
      c.removeAttribute("data-inited");
      delete c.dataset.inited;
    });

    
    // ✅ RESET flag "cardsInited" pada cards carousel hasil swap
    container.querySelectorAll("[data-cards-carousel]").forEach((cc) => {
      cc.removeAttribute("data-cards-inited"); // (tidak wajib, aman aja)
      delete cc.dataset.cardsInited;
    });

    // ✅ RESET flag drag badge hasil swap
    container.querySelectorAll("[data-drag-scroll]").forEach((el) => {
      el.removeAttribute("data-drag-inited");
      delete el.dataset.dragInited;
    });

    // update active styles
    tabs.forEach((btn) => {
      const isActive = btn.dataset.priceTab === key;

      btn.classList.toggle("text-sky-600", isActive);
      btn.classList.toggle("text-slate-500", !isActive);
      btn.classList.toggle("hover:text-slate-700", !isActive);

      const underline = btn.querySelector("span");
      if (underline) {
        underline.classList.toggle("bg-sky-600", isActive);
        underline.classList.toggle("bg-transparent", !isActive);
      }
    });

    // init ulang carousel untuk konten yang baru masuk
    window.initCarousels();
    initRequestCardsCarousel();
    initDragScrollBadges();
  };

  // default aktif 300 Juta
  render("300");

  // tab click
  tabs.forEach((btn) => {
    btn.addEventListener("click", () => {
      render(btn.dataset.priceTab);
    });
  });
}

function initYearFilterNoRefresh() {
  const section = document.querySelector("[data-year-section]");
  if (!section) return;

  const container = section.querySelector("#year-cards");
  const tabs = section.querySelectorAll("[data-year-tab]");

  if (!container || tabs.length === 0) return;

  const render = (key) => {
    const tpl = section.querySelector(`[data-year-template="${key}"]`);
    if (!tpl) return;

    container.innerHTML = tpl.innerHTML;

    // reset inited untuk carousel hasil swap
    container.querySelectorAll("[data-carousel]").forEach((c) => {
      c.removeAttribute("data-inited");
      delete c.dataset.inited;
    });
    
    container.querySelectorAll("[data-cards-carousel]").forEach((cc) => {
      cc.removeAttribute("data-cards-inited"); // (tidak wajib, aman aja)
      delete cc.dataset.cardsInited;
    });

    // ✅ RESET flag drag badge hasil swap
    container.querySelectorAll("[data-drag-scroll]").forEach((el) => {
      el.removeAttribute("data-drag-inited");
      delete el.dataset.dragInited;
    });

    // update active tab style
    tabs.forEach((btn) => {
      const isActive = btn.dataset.yearTab === key;

      btn.classList.toggle("text-sky-600", isActive);
      btn.classList.toggle("text-slate-500", !isActive);
      btn.classList.toggle("hover:text-slate-700", !isActive);

      const underline = btn.querySelector("span");
      if (underline) {
        underline.classList.toggle("bg-sky-600", isActive);
        underline.classList.toggle("bg-transparent", !isActive);
      }
    });

    window.initCarousels();
    initRequestCardsCarousel();
    initDragScrollBadges();
  };

  // default aktif 3 tahun
  render("3");

  tabs.forEach((btn) => {
    btn.addEventListener("click", () => {
      render(btn.dataset.yearTab);
    });
  });
}

function initRequestCardsCarousel() {
  const carousels = document.querySelectorAll("[data-cards-carousel]");
  if (!carousels.length) return;

  carousels.forEach((carousel) => {
    // ✅ Hindari double init (misal kalau ada render ulang / partial load)
    if (carousel.dataset.cardsInited === "1") return;
    carousel.dataset.cardsInited = "1";

    const track = carousel.querySelector("[data-cards-track]");
    const viewport = carousel.querySelector("[data-cards-viewport]");
    const dotsWrap = carousel.querySelector("[data-cards-dots]");
    const dots = dotsWrap ? dotsWrap.querySelectorAll("[data-cards-dot]") : [];

    if (!track || !viewport) return;

    // ✅ helper: jumlah halaman bisa berubah (mis. responsive / rerender)
    const getPages = () => track.children.length;

    // ✅ helper: baca index real-time dari dataset biar selalu sinkron
    const getIndex = () => parseInt(track.dataset.index || "0", 10) || 0;

    const setIndex = (i) => {
      const pages = getPages();
      let index = i;

      // ✅ looping / wrap
      if (pages <= 1) index = 0;
      else if (index < 0) index = pages - 1;
      else if (index >= pages) index = 0;

      track.dataset.index = String(index);
      track.style.transform = `translateX(-${index * 100}%)`;

      dots.forEach((d, di) => {
        d.classList.toggle("bg-sky-600", di === index);
        d.classList.toggle("bg-slate-300", di !== index);
      });
    };

    // click dots
    dots.forEach((d) => {
      d.addEventListener("click", () => {
        const i = parseInt(d.dataset.cardsDot, 10);
        setIndex(i);
      });
    });

    // ✅ penting: jika user swipe di dalam image carousel (data-carousel),
    // cards carousel TIDAK boleh ikut menangkap gesture
    const shouldIgnoreGesture = (e) => {
      const target = e.target;
      if (!target || !target.closest) return false;

      // ✅ jika gesture berasal dari image carousel, biarkan dia handle sendiri
      if (target.closest("[data-carousel]")) return true;

      // ✅ jika gesture berasal dari badge drag-scroll, JANGAN swipe cards carousel
      if (target.closest("[data-drag-scroll]")) return true;

      return false;
    };


    let startX = 0;
    let isDragging = false;

    const getX = (e) => (e.touches ? e.touches[0].clientX : e.clientX);

    const onStart = (e) => {
      // ✅ biarkan image carousel handle sendiri
      if (shouldIgnoreGesture(e)) return;

      isDragging = true;
      startX = getX(e);
    };

    const onEnd = (e) => {
      if (!isDragging) return;
      isDragging = false;

      const endX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
      const deltaX = endX - startX;

      if (Math.abs(deltaX) > 40) {
        const current = getIndex(); // ✅ selalu pakai index terbaru
        if (deltaX < 0) setIndex(current + 1); // swipe kiri -> next (looping)
        else setIndex(current - 1);            // swipe kanan -> prev (looping)
      }
    };

    // touch
    viewport.addEventListener("touchstart", onStart, { passive: true });
    viewport.addEventListener("touchend", onEnd, { passive: true });

    // mouse
    viewport.addEventListener("mousedown", onStart);
    viewport.addEventListener("mouseup", onEnd);
    viewport.addEventListener("mouseleave", () => (isDragging = false));

    // init posisi awal
    setIndex(getIndex());
  });
}

function initIconCarousel() {
  const viewports = document.querySelectorAll("[data-icon-carousel]");
  if (!viewports.length) return;

  viewports.forEach((viewport) => {
    if (viewport.dataset.iconInited === "1") return;
    viewport.dataset.iconInited = "1";

    const track =
      viewport.querySelector("[data-icon-track]") || viewport.firstElementChild;
    if (!track) return;

    const originals = Array.from(track.children);
    if (originals.length < 2) return;

    // ===== helpers ukuran =====
    const getGap = () => {
      const cs = window.getComputedStyle(track);
      const g = parseFloat(cs.columnGap || cs.gap || "0");
      return Number.isFinite(g) ? g : 0;
    };

    const getItemFullWidth = () => {
      const first = track.children[0];
      if (!first) return 0;
      return first.getBoundingClientRect().width + getGap();
    };

    // ===== buat buffer clone BESAR supaya terasa infinite =====
    const computeCloneCount = () => {
      const itemW = getItemFullWidth() || 1;
      const itemsPerViewport = Math.ceil(viewport.clientWidth / itemW);
      const needForScreens = itemsPerViewport * 3; // buffer 3 layar
      return Math.min(
        Math.max(originals.length * 2, needForScreens),
        originals.length * 6
      );
    };

    // setup clone hanya sekali
    if (track.dataset.iconCloned !== "1") {
      const cloneCount = computeCloneCount();

      const head = [];
      for (let i = 0; i < cloneCount; i++) {
        const src =
          originals[
            (originals.length - ((cloneCount - i) % originals.length)) %
              originals.length
          ];
        head.push(src.cloneNode(true));
      }

      const tail = [];
      for (let i = 0; i < cloneCount; i++) {
        const src = originals[i % originals.length];
        tail.push(src.cloneNode(true));
      }

      head.forEach((c) => track.insertBefore(c, track.firstChild));
      tail.forEach((c) => track.appendChild(c));

      track.dataset.iconCloned = "1";
      track.dataset.iconOriginalCount = String(originals.length);
      track.dataset.iconCloneCount = String(cloneCount);
    }

    const originalCount = parseInt(track.dataset.iconOriginalCount, 10);
    const cloneCount = parseInt(track.dataset.iconCloneCount, 10);

    const originalWidth = () => getItemFullWidth() * originalCount;
    const cloneWidth = () => getItemFullWidth() * cloneCount;

    // posisikan di tengah area asli
    const jumpToMiddle = () => {
      const cw = cloneWidth();
      if (cw > 0) viewport.scrollLeft = cw;
    };

    jumpToMiddle();

    // =========================================================
    // DRAG vs CLICK (lebih ketat, supaya "klik kecil" tidak nempel)
    // - Drag dinilai dari:
    //   a) delta pointer >= THRESHOLD_PX
    //   b) ATAU scrollLeft berubah >= THRESHOLD_SCROLL (walau pointer kecil)
    // =========================================================
    let isDown = false;
    let startX = 0;
    let startScrollLeft = 0;
    let didDrag = false;
    let didMove = false; // gerak kecil (untuk touch)
    let dragResetTimer = null;

    const THRESHOLD_PX = 4;       // lebih kecil dari 8 supaya cepat dianggap drag
    const THRESHOLD_SCROLL = 2;   // kalau scrollLeft berubah >= 2px -> drag

    const getClientX = (e) => (e.touches ? e.touches[0].clientX : e.clientX);

    const setDragging = () => {
      if (didDrag) return;
      didDrag = true;
      // ✅ matikan pointer events anak selama drag biar link tidak "nempel"
      track.style.pointerEvents = "none";
    };

    const clearDragging = () => {
      // balikin pointer events
      track.style.pointerEvents = "";

      // jangan langsung false, biar click "telat" kebatalkan dulu
      if (dragResetTimer) clearTimeout(dragResetTimer);
      dragResetTimer = setTimeout(() => {
        didDrag = false;
        didMove = false;
      }, 80);
    };

    const onDown = (e) => {
      isDown = true;
      didDrag = false;
      didMove = false;

      startX = getClientX(e);
      startScrollLeft = viewport.scrollLeft;

      // cancel reset timer kalau user mulai interaksi lagi
      if (dragResetTimer) {
        clearTimeout(dragResetTimer);
        dragResetTimer = null;
      }
    };

    const onMove = (e) => {
      if (!isDown) return;

      const x = getClientX(e);
      const delta = x - startX;

      // tandai pernah bergerak (khusus touch supaya ghost click lebih aman)
      if (Math.abs(delta) > 0) didMove = true;

      // drag jika pointer melewati threshold
      if (Math.abs(delta) >= THRESHOLD_PX) setDragging();

      // jika scrollLeft sudah berubah sedikit, anggap drag juga
      const nextScroll = startScrollLeft - delta;
      if (Math.abs(nextScroll - startScrollLeft) >= THRESHOLD_SCROLL) setDragging();

      if (didDrag) {
        // prevent text selection / native scroll behavior
        e.preventDefault();
        viewport.scrollLeft = nextScroll;
      }
    };

    const onUp = () => {
      if (!isDown) return;
      isDown = false;

      // selalu restore pointer events
      clearDragging();
    };

    // Mouse
    viewport.addEventListener("mousedown", onDown);
    window.addEventListener("mousemove", onMove, { passive: false });
    window.addEventListener("mouseup", onUp);

    // Touch
    viewport.addEventListener("touchstart", onDown, { passive: true });
    viewport.addEventListener("touchmove", onMove, { passive: false });
    viewport.addEventListener("touchend", onUp, { passive: true });
    viewport.addEventListener("touchcancel", onUp, { passive: true });

    // ✅ batalkan click jika sebelumnya drag / move
    viewport.addEventListener(
      "click",
      (e) => {
        // didMove membantu menghindari ghost click pada touch
        if (!didDrag && !didMove) return;
        e.preventDefault();
        e.stopPropagation();
      },
      true
    );

    // ===== SEAMLESS RECENTER (PROAKTIF) =====
    let rafLock = false;

    const onScroll = () => {
      if (rafLock) return;
      rafLock = true;

      requestAnimationFrame(() => {
        rafLock = false;

        const ow = originalWidth();
        const cw = cloneWidth();
        if (ow <= 0 || cw <= 0) return;

        // jaga selalu di "zona aman" supaya tidak pernah mentok scrollbar
        const leftSafe = cw - ow * 0.5;
        const rightSafe = cw + ow * 1.5;

        if (viewport.scrollLeft < leftSafe) {
          viewport.scrollLeft += ow;
        } else if (viewport.scrollLeft > rightSafe) {
          viewport.scrollLeft -= ow;
        }
      });
    };

    viewport.addEventListener("scroll", onScroll, { passive: true });

    // resize -> recenter
    window.addEventListener("resize", () => {
      jumpToMiddle();
    });

    // OPTIONAL: tombol panah tetap bisa dipakai (kalau kamu masih mau)
    const wrapper = viewport.parentElement;
    const btnPrev = wrapper.querySelector("[data-icon-prev]");
    const btnNext = wrapper.querySelector("[data-icon-next]");

    // ✅ step = 1 card + gap (tepat 1 item)
    const getStepOneCard = () => {
      // pakai helper yang sudah ada: getItemFullWidth()
      const w = getItemFullWidth();
      return w > 0 ? w : 240; // fallback aman
    };

    if (btnPrev) {
      btnPrev.addEventListener("click", (e) => {
        e.preventDefault();
        viewport.scrollBy({ left: -getStepOneCard(), behavior: "smooth" });
      });
    }

    if (btnNext) {
      btnNext.addEventListener("click", (e) => {
        e.preventDefault();
        viewport.scrollBy({ left: getStepOneCard(), behavior: "smooth" });
      });
    }

  });
}


function initDragScrollBadges() {
  const els = document.querySelectorAll("[data-drag-scroll]");
  if (!els.length) return;

  els.forEach((el) => {
    // ✅ hindari double init (penting karena ada render ulang)
    if (el.dataset.dragInited === "1") return;
    el.dataset.dragInited = "1";

    let isDown = false;
    let startX = 0;
    let startScrollLeft = 0;
    let didDrag = false;

    const THRESHOLD = 6; // px: gerak segini baru dianggap drag

    const getX = (e) => (e.touches ? e.touches[0].clientX : e.clientX);

    const onDown = (e) => {
      // ✅ cegah event naik ke cards carousel
      e.preventDefault();
      e.stopPropagation();

      isDown = true;
      didDrag = false;
      startX = getX(e);
      startScrollLeft = el.scrollLeft;

      el.classList.add("cursor-grabbing");
    };

    const onMove = (e) => {
      if (!isDown) return;

      const x = getX(e);
      const delta = x - startX;

      if (Math.abs(delta) > THRESHOLD) didDrag = true;

      if (didDrag) {
        // ✅ cegah parent carousel / browser gesture ikut jalan
        e.preventDefault();
        e.stopPropagation();

        el.scrollLeft = startScrollLeft - delta;
      }
    };

    const onUp = (e) => {
      if (!isDown) return;
      isDown = false;

      if (e) e.stopPropagation();
      el.classList.remove("cursor-grabbing");

      // ✅ kalau drag, blok click supaya gak “kepencet” link/badge
      // (capture phase)
      el.dataset.didDrag = didDrag ? "1" : "0";
      setTimeout(() => {
        el.dataset.didDrag = "0";
      }, 80);
    };

    // Mouse
    el.addEventListener("mousedown", onDown);
    window.addEventListener("mousemove", onMove, { passive: false });
    window.addEventListener("mouseup", onUp);

    // Touch
    el.addEventListener("touchstart", onDown, { passive: false });
    el.addEventListener("touchmove", onMove, { passive: false });
    el.addEventListener("touchend", onUp, { passive: true });
    el.addEventListener("touchcancel", onUp, { passive: true });

    // ✅ batalkan click kalau habis drag
    el.addEventListener(
      "click",
      (e) => {
        if (el.dataset.didDrag === "1") {
          e.preventDefault();
          e.stopPropagation();
        }
      },
      true
    );
  });
}

function initLikeButtons() {
  // event delegation: sekali pasang, berlaku untuk konten hasil render ulang
  if (document.body.dataset.likeInited === "1") return;
  document.body.dataset.likeInited = "1";

  const getCsrf = () => {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute("content") : "";
  };

  document.body.addEventListener("click", async (e) => {
    const btn = e.target.closest(".like-btn");
    if (!btn) return;

    e.preventDefault();

    const type = btn.dataset.type; // "mobil" | "motor"
    const idOrSlug =
      type === "mobil" ? btn.dataset.mobilId : btn.dataset.motorId;

    if (!type || !idOrSlug) return;

    try {
      const res = await fetch(`/${type}/${idOrSlug}/like-toggle`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": getCsrf(),
          Accept: "application/json",
        },
      });

      // kalau belum login, backend biasanya 401/419 atau redirect
      if (res.status === 401 || res.status === 419) {
        // kalau kamu pakai alpine store authModal
        if (window.Alpine?.store) {
          window.Alpine.store("authModal").open("login");
        }
        return;
      }

      if (!res.ok) {
        // fallback: biar gampang debug
        console.error("Like toggle failed:", res.status);
        return;
      }

      const data = await res.json();

      // ===== update heart icon =====
      const heart =
        btn.querySelector("[data-heart-icon]") ||
        btn.querySelector("i.bi"); // fallback untuk bootstrap icon

      if (heart) {
        const liked = !!data.liked;

        // support bootstrap icon class color
        heart.classList.toggle("text-red-500", liked);
        heart.classList.toggle("text-slate-400", !liked);
      }

      // ===== update count (kalau ada elemen count) =====
      // NOTE: card iklan profile kamu TIDAK punya count -> aman karena dicek null
      const countKey = btn.dataset.likeCountKey || idOrSlug;
      const countEl = document.querySelector(
        `[data-like-count="${countKey}"]`
      );

      if (countEl && data.likes_count !== undefined) {
        countEl.textContent = data.likes_count;
      }
    } catch (err) {
      console.error("Like toggle error:", err);
    }
  });
}

/**
 * BOOTSTRAP ALL
 */
document.addEventListener("DOMContentLoaded", () => {
  initNavbarScroll();
  window.initCarousels();         // init carousel non-template
  initPriceFilterNoRefresh(); 
  initYearFilterNoRefresh(); 
  initRequestCardsCarousel();   // render 300 + init carousel hasil render
  initIconCarousel();
  initDragScrollBadges();
  initLikeButtons();
});

