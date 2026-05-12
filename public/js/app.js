// Confettiscript, voor op de pagina post-update.html
window.addEventListener('load', () => {
  const duration = 2 * 1000;
  const end = Date.now() + duration;

  (function frame() {
    confetti({
      particleCount: 6,
      angle: 70,
      spread: 90,
      startVelocity: 45,
      origin: { x: 0, y: 0.6 }
    });
    confetti({
      particleCount: 6,
      angle: 110,
      spread: 90,
      startVelocity: 45,
      origin: { x: 1, y: 0.6 }
    });

    if (Date.now() < end) {
      requestAnimationFrame(frame);
    }
  })();
});
