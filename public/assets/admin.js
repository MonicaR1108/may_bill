(() => {
  const STORAGE_KEY = "admin_sidebar_collapsed";
  const toggleButton = document.querySelector("[data-sidebar-toggle]");
  const backdrop = document.querySelector("[data-sidebar-backdrop]");
  const backToTop = document.querySelector("[data-back-to-top]");
  const pageLoader = document.querySelector("[data-page-loader]");
  const prefersReducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const apply = (collapsed) => {
    document.body.classList.toggle("sidebar-collapsed", collapsed);
  };

  const initial = localStorage.getItem(STORAGE_KEY) === "1";
  apply(initial);

  if (toggleButton) {
    toggleButton.addEventListener("click", () => {
      const isMobile = window.matchMedia("(max-width: 991.98px)").matches;
      if (isMobile) {
        document.body.classList.toggle("sidebar-open");
        return;
      }

      const next = !document.body.classList.contains("sidebar-collapsed");
      localStorage.setItem(STORAGE_KEY, next ? "1" : "0");
      apply(next);
    });
  }

  // Sidebar groups (Masters, etc.).
  const groupToggles = Array.from(document.querySelectorAll("[data-sidebar-group]"));
  groupToggles.forEach((btn) => {
    const groupKey = btn.getAttribute("data-sidebar-group");
    if (!groupKey) return;

    const group = btn.closest(".sidebar-group");
    if (!group) return;

    const storageKey = `admin_sidebar_group_${groupKey}`;

    const setOpen = (open) => {
      group.classList.toggle("sidebar-group-open", open);
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    };

    const serverOpen = group.classList.contains("sidebar-group-open");
    const stored = localStorage.getItem(storageKey);
    if (!serverOpen && stored !== null) {
      setOpen(stored === "1");
    } else if (serverOpen) {
      setOpen(true);
    }

    btn.addEventListener("click", () => {
      const next = !group.classList.contains("sidebar-group-open");
      setOpen(next);
      localStorage.setItem(storageKey, next ? "1" : "0");
    });
  });

  if (backdrop) {
    backdrop.addEventListener("click", () => {
      document.body.classList.remove("sidebar-open");
    });
  }

  // Close sidebar on outside click (mobile).
  document.addEventListener("click", (e) => {
    const isMobile = window.matchMedia("(max-width: 991.98px)").matches;
    if (!isMobile) return;
    if (!document.body.classList.contains("sidebar-open")) return;

    const sidebar = document.querySelector(".admin-sidebar");
    if (!sidebar) return;

    const clickedToggle = e.target && e.target.closest && e.target.closest("[data-sidebar-toggle]");
    if (clickedToggle) return;

    const clickedInsideSidebar = sidebar.contains(e.target);
    if (!clickedInsideSidebar) {
      document.body.classList.remove("sidebar-open");
    }
  });

  // Back to top + smooth scroll.
  if (backToTop) {
    let ticking = false;

    const updateBackToTop = () => {
      ticking = false;
      const show = window.scrollY > 320;
      backToTop.classList.toggle("show", show);
    };

    window.addEventListener(
      "scroll",
      () => {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateBackToTop);
      },
      { passive: true }
    );

    updateBackToTop();

    backToTop.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: prefersReducedMotion ? "auto" : "smooth" });
    });
  }

  // Page loader for navigations (lightweight UX).
  const setLoading = (on) => {
    if (!pageLoader) return;
    document.body.classList.toggle("page-loading", on);
  };

  window.addEventListener("pageshow", () => setLoading(false));

  // Custom delete confirmation modal (avoid browser confirm).
  const confirmDeleteModal = document.getElementById("confirmDeleteModal");
  if (confirmDeleteModal) {
    const messageEl = confirmDeleteModal.querySelector("[data-confirm-message]");
    const submitBtn = confirmDeleteModal.querySelector("[data-confirm-submit]");
    const deleteForms = Array.from(document.querySelectorAll("form.js-confirm-delete"));
    const fallbackMessage = "Do you want to delete this item?";
    const bsModal = typeof bootstrap !== "undefined" ? new bootstrap.Modal(confirmDeleteModal) : null;
    let pendingForm = null;

    const resetModalState = () => {
      pendingForm = null;
      if (submitBtn) submitBtn.disabled = false;
    };

    deleteForms.forEach((form) => {
      form.addEventListener("submit", (e) => {
        if (!bsModal) return;
        e.preventDefault();
        pendingForm = form;
        const msg = form.getAttribute("data-confirm-message") || fallbackMessage;
        if (messageEl) messageEl.textContent = msg;
        bsModal.show();
      });
    });

    if (submitBtn) {
      submitBtn.addEventListener("click", () => {
        if (!pendingForm) return;
        submitBtn.disabled = true;
        pendingForm.submit();
      });
    }

    confirmDeleteModal.addEventListener("hidden.bs.modal", resetModalState);
  }

  document.addEventListener("click", (e) => {
    const a = e.target && e.target.closest ? e.target.closest("a") : null;
    if (!a) return;
    if (a.hasAttribute("data-no-loader")) return;

    const href = a.getAttribute("href") || "";
    if (!href || href.startsWith("#") || href.startsWith("javascript:")) return;
    if (a.target && a.target.toLowerCase() === "_blank") return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

    // Same-origin only.
    try {
      const url = new URL(href, window.location.href);
      if (url.origin !== window.location.origin) return;
    } catch {
      return;
    }

    setLoading(true);
  });

  // Animated counters (dashboard stats).
  const animateCounter = (el) => {
    if (el.dataset.counterDone === "1") return;

    const raw = (el.textContent || "").trim();
    const target = Number(raw.replace(/,/g, ""));
    if (!Number.isFinite(target)) return;

    el.dataset.counterDone = "1";
    if (prefersReducedMotion) return;

    const start = 0;
    const durationMs = 850;
    const startTime = performance.now();
    const formatter = new Intl.NumberFormat(undefined);

    const tick = (t) => {
      const p = Math.min(1, (t - startTime) / durationMs);
      const eased = 1 - Math.pow(1 - p, 3);
      const current = Math.round(start + (target - start) * eased);
      el.textContent = formatter.format(current);
      if (p < 1) window.requestAnimationFrame(tick);
    };

    el.textContent = "0";
    window.requestAnimationFrame(tick);
  };

  const counterEls = Array.from(document.querySelectorAll("[data-counter]"));
  if (counterEls.length) {
    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver(
        (entries) => {
          for (const entry of entries) {
            if (entry.isIntersecting) animateCounter(entry.target);
          }
        },
        { threshold: 0.35 }
      );

      counterEls.forEach((el) => io.observe(el));
    } else {
      counterEls.forEach(animateCounter);
    }
  }

  // Tooltips for collapsed sidebar (show on hover only when collapsed).
  const tooltipTargets = Array.from(document.querySelectorAll("[data-collapsed-tooltip]"));
  if (tooltipTargets.length && typeof bootstrap !== "undefined") {
    const tooltips = new Map();
    const popovers = new Map();

    const buildMenuHtml = (items = [], title = "") => {
      if (!items.length) return "";
      const links = items
        .map((item) => `<a class="sidebar-tooltip-link" href="${item.href}">${item.label}</a>`)
        .join("");
      const heading = title ? `<div class="sidebar-tooltip-title">${title}</div>` : "";
      return `<div class="sidebar-tooltip-menu">${heading}${links}</div>`;
    };

    tooltipTargets.forEach((el) => {
      const title = el.getAttribute("data-collapsed-tooltip") || "";
      if (!title) return;
      if (el.hasAttribute("title")) {
        el.removeAttribute("title");
      }

      const itemsRaw = el.getAttribute("data-collapsed-tooltip-items");
      const items = itemsRaw ? JSON.parse(itemsRaw) : [];

      if (items.length) {
        const instance = new bootstrap.Popover(el, {
          trigger: "manual",
          placement: "right",
          html: true,
          sanitize: false,
          content: buildMenuHtml(items, title),
          customClass: "sidebar-tooltip-popover",
        });
        popovers.set(el, instance);
      } else {
        el.setAttribute("data-bs-toggle", "tooltip");
        el.setAttribute("data-bs-title", title);
        const instance = new bootstrap.Tooltip(el, {
          trigger: "manual",
          placement: "right",
          customClass: "sidebar-tooltip-popover",
        });
        tooltips.set(el, instance);
      }
    });

    const setTooltipState = () => {
      const collapsed = document.body.classList.contains("sidebar-collapsed");
      tooltips.forEach((tt) => (collapsed ? tt.enable() : (tt.hide(), tt.disable())));
      popovers.forEach((pp) => (collapsed ? pp.enable() : (pp.hide(), pp.disable())));
    };

    const attachHover = (el, instance, type) => {
      let hideTimer;
      const show = () => {
        if (!document.body.classList.contains("sidebar-collapsed")) return;
        if (hideTimer) window.clearTimeout(hideTimer);
        instance.show();
      };
      const scheduleHide = () => {
        hideTimer = window.setTimeout(() => {
          const tip = instance.getTipElement ? instance.getTipElement() : null;
          if (tip && tip.matches && tip.matches(":hover")) return;
          instance.hide();
        }, 400);
      };

      el.addEventListener("mouseenter", show);
      el.addEventListener("mouseleave", scheduleHide);

      if (type === "popover") {
        el.addEventListener("shown.bs.popover", () => {
          const tip = instance.getTipElement();
          if (!tip) return;
          tip.addEventListener("mouseenter", () => {
            if (hideTimer) window.clearTimeout(hideTimer);
          });
          tip.addEventListener("mouseleave", scheduleHide);
        });
      }
    };

    tooltips.forEach((instance, el) => attachHover(el, instance, "tooltip"));
    popovers.forEach((instance, el) => attachHover(el, instance, "popover"));

    setTooltipState();

    if (toggleButton) {
      toggleButton.addEventListener("click", () => {
        window.setTimeout(setTooltipState, 0);
      });
    }
  }

  // Auto-submit search forms while typing (with debounce).
  const autoSubmitForms = Array.from(document.querySelectorAll("form[data-auto-submit]"));
  if (autoSubmitForms.length) {
    const debounce = (fn, wait) => {
      let t;
      return (...args) => {
        window.clearTimeout(t);
        t = window.setTimeout(() => fn(...args), wait);
      };
    };

    autoSubmitForms.forEach((form) => {
      const input = form.querySelector("input[type='text'], input[type='search']");
      if (!input) return;

      const submit = debounce(() => {
        if (document.activeElement === input) form.requestSubmit();
      }, 350);

      input.addEventListener("input", submit);
    });
  }
})();
