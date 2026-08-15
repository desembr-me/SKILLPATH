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
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  document.querySelectorAll('.flash').forEach(el => setTimeout(() => el.remove(), 5000));
});
