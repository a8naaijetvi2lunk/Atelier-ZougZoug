/* ============================================================
   Atelier ZougZoug — Page À propos
   - GSAP ScrollTrigger parallax sur images
   - Texte fade-in au scroll
   - Hero content fade-in
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);

// 1. Parallax sur les images des blocs diptyques
document.querySelectorAll('.about-half--visual img').forEach(function (img) {
  gsap.to(img, {
    yPercent: isMobile ? -8 : -15,
    ease: 'none',
    scrollTrigger: {
      trigger: img.closest('.about-block'),
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    }
  });
});

// 2. Texte : fade in + slide up au scroll
document.querySelectorAll('.about-half--content').forEach(function (content) {
  var children = content.children;
  for (var i = 0; i < children.length; i++) {
    (function (el, index) {
      gsap.from(el, {
        opacity: 0,
        y: 30,
        duration: 0.8,
        delay: index * 0.1,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: content,
          start: 'top 75%',
          toggleActions: 'play none none reverse',
        }
      });
    })(children[i], i);
  }
});

// 3. Hero content : fade in au chargement
(function () {
  var heroContent = document.querySelector('.about-hero-content');
  if (!heroContent) return;
  var children = heroContent.children;
  for (var i = 0; i < children.length; i++) {
    gsap.from(children[i], {
      opacity: 0,
      y: 25,
      duration: 1,
      delay: 0.3 + i * 0.15,
      ease: 'power3.out',
    });
  }
})();
