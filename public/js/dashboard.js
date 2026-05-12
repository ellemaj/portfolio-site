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
  const rows = document.querySelectorAll(".dashboard_section table tr");
  const progressBar = document.querySelector(".progress");
  const ecText = document.querySelector(".dashboard_section p");

  let totalEC = 0;
  const maxEC = 60;
  const nbsaGrens = 45;

  rows.forEach((row) => {
    const ecCell = row.querySelector(".ec");
    const gradeCell = row.querySelector(".grade");

    if (!ecCell || !gradeCell) return;

    const ecValue = parseFloat(ecCell.textContent.replace(",", "."));

    let grade = null;
    const input = gradeCell.querySelector(".grade-input");

    if (input) {
      grade = parseFloat(input.value);
    } else {
      grade = parseFloat(gradeCell.textContent.replace(",", "."));
    }

    // reset classes
    row.querySelectorAll("td").forEach(td => {
      td.classList.remove("behaald", "onvoldoende");
    });

    if (!isNaN(grade) && grade >= 5.5) {
      totalEC += ecValue;
      row.querySelectorAll("td").forEach(td => td.classList.add("behaald"));
    } else if (!isNaN(grade)) {
      row.querySelectorAll("td").forEach(td => td.classList.add("onvoldoende"));
    }
  });

  // progress bar
  const percentage = Math.min((totalEC / maxEC) * 100, 100);
  progressBar.style.width = `${percentage}%`;

  // tekst
  ecText.textContent = `${totalEC.toFixed(1)} / ${maxEC} EC behaald`;

  // confetti
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