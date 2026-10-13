function showFieldError(inputId, errorId) {
    document.getElementById(inputId).classList.add('input-invalid');
    document.getElementById(errorId).classList.remove('hidden');
}

function clearFieldError(inputId, errorId) {
    document.getElementById(inputId).classList.remove('input-invalid');
    document.getElementById(errorId).classList.add('hidden');
}

const nameInput    = document.getElementById('name');
const emailInput   = document.getElementById('email');
const messageInput = document.getElementById('message');

nameInput.addEventListener('blur', () => {
    if (!nameInput.value.trim()) showFieldError('name', 'name-error');
});
nameInput.addEventListener('input', () => clearFieldError('name', 'name-error'));

emailInput.addEventListener('blur', () => {
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        showFieldError('email', 'email-error');
    }
});
emailInput.addEventListener('input', () => clearFieldError('email', 'email-error'));

messageInput.addEventListener('blur', () => {
    if (!messageInput.value.trim()) showFieldError('message', 'message-error');
});
messageInput.addEventListener('input', () => clearFieldError('message', 'message-error'));

document.getElementById('contact-form').addEventListener('submit', (e) => {
    let hasError = false;

    if (!nameInput.value.trim()) {
        showFieldError('name', 'name-error');
        hasError = true;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        showFieldError('email', 'email-error');
        hasError = true;
    }
    if (!messageInput.value.trim()) {
        showFieldError('message', 'message-error');
        hasError = true;
    }

    if (hasError) e.preventDefault();
});
