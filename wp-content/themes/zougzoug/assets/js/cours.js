/* ============================================================
   Atelier ZougZoug — Page Cours
   - GSAP animations (hero, intro, offres, galerie)
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);

// 1. Hero — Parallax leger sur la photo
(function () {
  var heroImg = document.querySelector('.cours-hero-img');
  if (!heroImg || isMobile) return;

  gsap.to(heroImg, {
    yPercent: 15,
    ease: 'none',
    scrollTrigger: {
      trigger: '.cours-hero',
      start: 'top top',
      end: 'bottom top',
      scrub: true
    }
  });
})();

// Hero content fade-in
(function () {
  var content = document.querySelector('.cours-hero-content');
  if (!content) return;
  var children = content.children;
  for (var i = 0; i < children.length; i++) {
    gsap.fromTo(children[i],
      { opacity: 0, y: 20 },
      { opacity: 1, y: 0, duration: 0.9, delay: 0.3 + i * 0.15, ease: 'power3.out' }
    );
  }
})();

// 2. Intro — Fade in au scroll
(function () {
  var inner = document.querySelector('.cours-intro-inner');
  if (!inner) return;
  var children = inner.children;
  for (var i = 0; i < children.length; i++) {
    gsap.fromTo(children[i],
      { opacity: 0, y: 30 },
      {
        opacity: 1, y: 0, duration: 0.8, delay: i * 0.12, ease: 'power3.out',
        scrollTrigger: {
          trigger: inner,
          start: 'top 80%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  }
})();

// 3. Offres — Cards stagger reveal
(function () {
  var cards = document.querySelectorAll('.offre-card');
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

// 4. Galerie — Reveal au scroll
(function () {
  var photos = document.querySelectorAll('.cours-galerie-photo');
  photos.forEach(function (photo, i) {
    gsap.fromTo(photo,
      { opacity: 0, scale: 0.95 },
      {
        opacity: 1, scale: 1, duration: 0.7, delay: i * 0.1, ease: 'power2.out',
        scrollTrigger: {
          trigger: photo,
          start: 'top 90%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  });
})();
