document.addEventListener('DOMContentLoaded', () => {
  const interestForm = document.getElementById('interestForm');
  if (interestForm) {
    const checks = [...interestForm.querySelectorAll('input[name="interests[]"]')];
    checks.forEach(check => check.addEventListener('change', () => {
      const selected = checks.filter(i => i.checked);
      if (selected.length > 3) {
        check.checked = false;
        alert('Pilih maksimal 3 minat agar rekomendasi tetap fokus.');
      }
    }));
  }

  // Enhanced Mobile Navigation Drawer & Toggle
  const toggleBtn = document.querySelector('[data-nav-toggle]');
  const drawer = document.querySelector('[data-nav-drawer]');
  const backdrop = document.querySelector('[data-nav-backdrop]');
  const closeBtn = document.querySelector('[data-nav-close]');
  const legacyNav = document.querySelector('[data-main-nav]');

  function openMobileNav() {
    if (drawer) drawer.classList.add('open');
    if (backdrop) backdrop.classList.add('open');
    if (toggleBtn) {
      toggleBtn.setAttribute('aria-expanded', 'true');
      toggleBtn.classList.add('active');
    }
    if (legacyNav) legacyNav.classList.add('open');
    document.body.classList.add('mobile-nav-locked');
  }

  function closeMobileNav() {
    if (drawer) drawer.classList.remove('open');
    if (backdrop) backdrop.classList.remove('open');
    if (toggleBtn) {
      toggleBtn.setAttribute('aria-expanded', 'false');
      toggleBtn.classList.remove('active');
    }
    if (legacyNav) legacyNav.classList.remove('open');
    document.body.classList.remove('mobile-nav-locked');
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = drawer ? drawer.classList.contains('open') : (legacyNav && legacyNav.classList.contains('open'));
      if (isOpen) {
        closeMobileNav();
      } else {
        openMobileNav();
      }
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      closeMobileNav();
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', () => {
      closeMobileNav();
    });
  }

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeMobileNav();
    }
  });

  // Close when clicking nav links
  if (drawer) {
    drawer.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        closeMobileNav();
      });
    });
  } else if (legacyNav) {
    legacyNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        closeMobileNav();
      });
    });
  }

  // Close on outside click if legacy nav fallback
  document.addEventListener('click', (e) => {
    if (drawer && drawer.classList.contains('open')) {
      if (!drawer.contains(e.target) && (!toggleBtn || !toggleBtn.contains(e.target))) {
        closeMobileNav();
      }
    }
  });

  document.querySelectorAll('.flash').forEach(el => setTimeout(() => el.remove(), 5000));
});
