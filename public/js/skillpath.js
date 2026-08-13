document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('is-open');
        });
    }

    const instructorToggle = document.querySelector('[data-instructor-toggle]');
    const instructorSidebar = document.querySelector('[data-instructor-sidebar]');

    if (instructorToggle && instructorSidebar) {
        instructorToggle.addEventListener('click', () => {
            instructorSidebar.classList.toggle('open');
        });
    }
});
