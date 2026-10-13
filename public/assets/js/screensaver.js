const clockTime = document.getElementById('clock-time');
const clockDate = document.getElementById('clock-date');

function createDigit(char) {
    const digit = document.createElement('span');
    digit.className = 'clock-digit';
    digit.textContent = char;
    // Remove the class afterwards so the animation can play again on the next change
    digit.addEventListener('animationend', () => digit.classList.remove('clock-digit-change'));
    return digit;
}

function renderTime(time) {
    // Build one span per character once, so later only the changed digits animate
    if (clockTime.children.length !== time.length) {
        clockTime.replaceChildren(...[...time].map(createDigit));
        return;
    }

    [...time].forEach((char, index) => {
        const digit = clockTime.children[index];
        if (digit.textContent === char) return;

        digit.textContent = char;
        digit.classList.add('clock-digit-change');
    });
}

function updateClock() {
    const now = new Date();

    renderTime(now.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' }));
    clockDate.textContent = now.toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' });
}

updateClock();
// Tick every second so the minute flips on time instead of up to 59s late
setInterval(updateClock, 1000);
