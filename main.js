// -----------------------------------------------------------------------------
// Fonction pour calculer l'âge à partir d'une date de naissance
// -----------------------------------------------------------------------------
function calculerAge(dateNaissance) {
  const aujourdHui = new Date();
  const naissance = new Date(dateNaissance);
  let age = aujourdHui.getFullYear() - naissance.getFullYear();
  const mois = aujourdHui.getMonth() - naissance.getMonth();
  if (mois < 0 || (mois === 0 && aujourdHui.getDate() < naissance.getDate())) {
    age--;
  }
  return age;
}

document.addEventListener("DOMContentLoaded", () => {
  const ageElement = document.getElementById("age");
  if (ageElement) {
    const ageNaia = calculerAge("2006-06-01");
    ageElement.textContent = " " + ageNaia + " ans";
  }
});

// -----------------------------------------------------------------------------
// Animations des cartes
// -----------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  gsap.fromTo(
    ".card",
    { scale: 0 },
    {
      scale: 1,
      stagger: 0.08,
      ease: "elastic.out(1, 0.8)",
      delay: 0.5
    }
  );

  const cards = document.querySelectorAll(".card");
  const transformStyles = [
    "rotate(10deg) translate(-170px)",
    "rotate(5deg) translate(-85px)",
    "rotate(-3deg)",
    "rotate(-10deg) translate(85px)",
    "rotate(2deg) translate(170px)"
  ];

  cards.forEach((card, idx) => {
    card.addEventListener("mouseenter", () => pushSiblings(idx));
    card.addEventListener("mouseleave", resetSiblings);
  });

  function pushSiblings(hoveredIdx) {
    cards.forEach((c, i) => {
      gsap.killTweensOf(c);
      const baseTransform = transformStyles[i] || "none";
      if (i === hoveredIdx) {
        gsap.to(c, {
          transform: baseTransform.replace(/rotate\([^)]*\)/, "rotate(0deg)"),
          duration: 0.4,
          ease: "back.out(1.4)"
        });
      } else {
        const offsetX = i < hoveredIdx ? -160 : 160;
        gsap.to(c, { x: offsetX, duration: 0.4, ease: "back.out(1.4)" });
      }
    });
  }

  function resetSiblings() {
    cards.forEach((c, i) => {
      gsap.killTweensOf(c);
      gsap.to(c, {
        transform: transformStyles[i],
        x: 0,
        duration: 0.4,
        ease: "back.out(1.4)"
      });
    });
  }
});

// -----------------------------------------------------------------------------
// Effet PixelBlast
// -----------------------------------------------------------------------------