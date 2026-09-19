const loadingBar = document.getElementById('page-loading-bar');

function startLoading() {
    loadingBar.classList.remove('is-loading');
    void loadingBar.offsetWidth;
    loadingBar.classList.add('is-loading');
}

function disableSubmitButton(form) {
    const btn = form.querySelector('[type="submit"]');
    if (btn && !btn.disabled) {
        btn.disabled = true;
        btn.dataset.original = btn.textContent;
        btn.textContent = 'Bezig...';
    }
}

document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (link) {
        const href = link.getAttribute('href') ?? '';
        if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
            startLoading();
        }
    }
});

document.querySelectorAll('form:not([data-confirm])').forEach(form => {
    form.addEventListener('submit', () => {
        startLoading();
        disableSubmitButton(form);
    });
});

const dialog = document.getElementById('confirm-dialog');
const confirmYes = document.getElementById('confirm-yes');
const confirmNo = document.getElementById('confirm-no');
let pendingForm = null;

document.querySelectorAll('[data-confirm]').forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        pendingForm = form;
        document.getElementById('confirm-message').textContent =
            form.dataset.confirm || 'Weet je zeker dat je dit wilt verwijderen?';
        dialog.showModal();
    });
});

confirmYes.addEventListener('click', () => {
    dialog.close();
    if (pendingForm) {
        startLoading();
        disableSubmitButton(pendingForm);
        pendingForm.submit();
    }
});

confirmNo.addEventListener('click', () => {
    dialog.close();
    pendingForm = null;
});

// Mobile menu toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
const menuIconOpen = document.getElementById('menu-icon-open');
const menuIconClose = document.getElementById('menu-icon-close');

mobileMenuBtn.addEventListener('click', () => {
    const isOpen = !mobileMenu.classList.contains('hidden');
    mobileMenu.classList.toggle('hidden', isOpen);
    menuIconOpen.classList.toggle('hidden', !isOpen);
    menuIconClose.classList.toggle('hidden', isOpen);
});
