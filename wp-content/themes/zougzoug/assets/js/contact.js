/* ============================================================
   Atelier ZougZoug — Page Contact
   - GSAP animations fade-in
   - Parallax sur la photo hero
   - Photos reveal au scroll
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);

// 1. Infos contact : fade in + slide up au chargement
(function () {
  var info = document.querySelector('.contact-half--info');
  if (!info) return;
  var children = info.children;
  for (var i = 0; i < children.length; i++) {
    gsap.from(children[i], {
      opacity: 0,
      y: 25,
      duration: 1,
      delay: 0.3 + i * 0.12,
      ease: 'power3.out',
    });
  }
})();

// 2. Parallax sur la photo hero
(function () {
  var heroImg = document.querySelector('.contact-half--visual img');
  if (!heroImg || isMobile) return;
  gsap.to(heroImg, {
    yPercent: -8,
    ease: 'none',
    scrollTrigger: {
      trigger: '.contact-hero',
      start: 'top top',
      end: 'bottom top',
      scrub: true,
    }
  });
})();

// 3. Formulaire : fade in au scroll
(function () {
  var formHeader = document.querySelector('.contact-form-header');
  var formEl = document.querySelector('.contact-form');
  if (!formHeader || !formEl) return;

  gsap.from(formHeader.children, {
    opacity: 0,
    y: 30,
    duration: 0.8,
    stagger: 0.12,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.contact-form-section',
      start: 'top 75%',
      toggleActions: 'play none none reverse',
    }
  });

  gsap.from(formEl.children, {
    opacity: 0,
    y: 20,
    duration: 0.7,
    stagger: 0.08,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.contact-form',
      start: 'top 80%',
      toggleActions: 'play none none reverse',
    }
  });
})();

// 4. Photos du bas : reveal au scroll
document.querySelectorAll('.contact-photo').forEach(function (photo, i) {
  gsap.from(photo, {
    opacity: 0,
    y: 30,
    duration: 1,
    delay: i * 0.15,
    ease: 'power3.out',
    scrollTrigger: {
      trigger: '.contact-photos',
      start: 'top 85%',
      toggleActions: 'play none none reverse',
    }
  });
});
