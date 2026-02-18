/* ============================================================
   Atelier ZougZoug — Page Revendeurs & Evenements
   - GSAP animations (hero, lieux, photos, agenda)
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);

// 1. Hero — Fade in du contenu
(function () {
  var inner = document.querySelector('.rev-hero-inner');
  if (!inner) return;
  var children = inner.children;
  for (var i = 0; i < children.length; i++) {
    gsap.fromTo(children[i],
      { opacity: 0, y: 20 },
      { opacity: 1, y: 0, duration: 0.9, delay: 0.2 + i * 0.15, ease: 'power3.out' }
    );
  }
})();

// 2. Points de vente — Cards stagger reveal
(function () {
  var cards = document.querySelectorAll('.rev-lieu-card');
  cards.forEach(function (card, i) {
    gsap.fromTo(card,
      { opacity: 0, y: 40 },
      {
        opacity: 1, y: 0, duration: 0.7, delay: i * 0.1, ease: 'power3.out',
        scrollTrigger: {
          trigger: card,
          start: 'top 88%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  });
})();

// 3. Bande photos — Reveal au scroll
(function () {
  var photos = document.querySelectorAll('.rev-photo');
  photos.forEach(function (photo, i) {
    gsap.fromTo(photo,
      { opacity: 0, scale: 0.95 },
      {
        opacity: 1, scale: 1, duration: 0.7, delay: i * 0.15, ease: 'power2.out',
        scrollTrigger: {
          trigger: photo,
          start: 'top 90%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  });
})();

// 4. Agenda — Events stagger reveal
(function () {
  var events = document.querySelectorAll('.rev-event');
  events.forEach(function (evt, i) {
    gsap.fromTo(evt,
      { opacity: 0, x: -20 },
      {
        opacity: function () {
          return evt.classList.contains('rev-event--past') ? 0.5 : 1;
        },
        x: 0, duration: 0.6, delay: i * 0.1, ease: 'power3.out',
        scrollTrigger: {
          trigger: evt,
          start: 'top 88%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  });
})();
