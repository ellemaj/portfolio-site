// Laad confetti-bibliotheek in
const confettiScript = document.createElement("script");
confettiScript.src = "https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js";
document.head.appendChild(confettiScript);

document.addEventListener("DOMContentLoaded", () => {
  updateProgress();
  document.addEventListener("input", updateProgress);
});

let confettiShown = false;

function updateProgress() {
  const rows = document.querySelectorAll("table tbody tr");
  const progressBar = document.getElementById("ec-progress-bar");
  const ecEarnedText = document.getElementById("ec-earned-text");
  const ecPercentText = document.getElementById("ec-percent-text");

  if (!progressBar || !ecEarnedText) return;

  let totalEC = 0;
  const maxEC = 60;
  const nbsaGrens = 45;

  rows.forEach((row) => {
    const ecCell = row.querySelector(".ec");
    const gradeCell = row.querySelector(".grade");

    if (!ecCell || !gradeCell) return;

    const ecValue = parseFloat(ecCell.textContent.trim().replace(",", "."));

    const input = gradeCell.querySelector(".grade-input");
    const grade = input
      ? parseFloat(input.value)
      : parseFloat(gradeCell.textContent.trim().replace(",", "."));

    if (!isNaN(grade) && grade >= 5.5) {
      totalEC += ecValue;
    }
  });

  const displayEC = Math.round(totalEC * 10) / 10;
  const percentage = Math.min((displayEC / maxEC) * 100, 100);
  const percentRounded = Math.round(percentage);

  progressBar.style.width = `${percentage}%`;
  ecEarnedText.textContent = `${Number.isInteger(displayEC) ? displayEC : displayEC.toFixed(1)} / ${maxEC} EC`;
  if (ecPercentText) ecPercentText.textContent = `${percentRounded}% behaald`;

  if (totalEC >= nbsaGrens && !confettiShown) {
    startConfetti();
    confettiShown = true;
  }
}

// 🎉 Confetti
function startConfetti() {
  const duration = 5 * 1000;
  const end = Date.now() + duration;

  (function frame() {
    confetti({
      particleCount: 8,
      angle: 70,
      spread: 100,
      startVelocity: 45,
      origin: { x: 0, y: 0.6 }
    });

    confetti({
      particleCount: 8,
      angle: 110,
      spread: 100,
      startVelocity: 45,
      origin: { x: 1, y: 0.6 }
    });

    if (Date.now() < end) {
      requestAnimationFrame(frame);
    }
  })();
}