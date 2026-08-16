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

  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-main-nav]');
  if (toggle && nav) {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
      if (nav.classList.contains('open') && !nav.contains(e.target) && !toggle.contains(e.target)) {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });

    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  document.querySelectorAll('.flash').forEach(el => setTimeout(() => el.remove(), 5000));
});
