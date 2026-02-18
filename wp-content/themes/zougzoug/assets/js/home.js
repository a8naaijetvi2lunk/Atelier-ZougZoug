/* ============================================================
   Atelier ZougZoug — Accueil
   - Swiper hero slider
   - GSAP ScrollTrigger animations
   ============================================================ */

(function () {
  'use strict';

  var isMobile = window.matchMedia('(max-width: 768px)').matches;

  // --- Swiper : slider vertical avec parallax diptyque ---
  var heroSwiper = new Swiper('#heroSwiper', {
    direction: 'vertical',
    loop: true,
    speed: 1000,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    parallax: true,
    effect: 'slide',
    allowTouchMove: false,
  });

  // --- GSAP ScrollTrigger ---
  gsap.registerPlugin(ScrollTrigger);

  // 1. Statement pinne — logo trace + texte highlight + photos reveal
  (function () {
    var textEl = document.querySelector('.parcours-text');
    if (!textEl) return;

    var words = textEl.textContent.trim().split(/\s+/);
    textEl.innerHTML = words.map(function (w) {
      return '<span class="parcours-word">' + w + '</span>';
    }).join(' ');

    var wordSpans = document.querySelectorAll('.parcours-word');
    var total = wordSpans.length;
    var logoPaths = document.querySelectorAll('.logo-trace');

    // Selectionner seulement les images visibles
    var images = [];
    document.querySelectorAll('.reveal-img').forEach(function (img) {
      if (img.offsetParent !== null || getComputedStyle(img).display !== 'none') {
        images.push(img);
      }
    });

    logoPaths.forEach(function (path) {
      var len = path.getTotalLength();
      path.style.strokeDasharray = len;
      path.style.strokeDashoffset = len;
    });

    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: '.exp-statement',
        pin: '.exp-statement-inner',
        start: 'top top',
        end: isMobile ? '+=150%' : '+=200%',
        scrub: 0.6,
      }
    });

    // Logo se trace (0 → 0.65)
    logoPaths.forEach(function (path) {
      tl.to(path, {
        strokeDashoffset: 0,
        duration: 0.65,
        ease: 'none',
      }, 0);
    });

    // Texte highlight mot par mot (0 → 0.6)
    wordSpans.forEach(function (span, i) {
      var pos = (i / total) * 0.6;
      tl.to(span, {
        color: '#1A1A1A',
        duration: 0.02,
        ease: 'none',
      }, pos);
    });

    // Photos reveal
    var imgConfigs;
    if (isMobile) {
      imgConfigs = [
        { y: 20, scale: 0.96, start: 0.1 },
        { y: -20, scale: 0.96, start: 0.4 },
      ];
    } else {
      imgConfigs = [
        { y: 30, scale: 0.95, start: 0.05 },
        { y: -25, scale: 0.95, start: 0.18 },
        { y: 35, scale: 0.93, start: 0.32 },
        { y: -30, scale: 0.94, start: 0.45 },
        { y: 25, scale: 0.92, start: 0.55 },
      ];
    }

    images.forEach(function (img, i) {
      if (i >= imgConfigs.length) return;
      var config = imgConfigs[i];
      gsap.set(img, { y: config.y, scale: config.scale });
      tl.to(img, {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 0.12,
        ease: 'power2.out',
      }, config.start);
    });

    // CTA "Decouvrir les collaborations"
    tl.to('.cta-projets-btn', {
      opacity: 1,
      y: 0,
      duration: 0.1,
      ease: 'power2.out',
    }, 0.65);

    // Hold
    tl.to({}, { duration: 0.25 }, 0.75);
  })();

  // 2. Luminaires — images reveal au scroll
  document.querySelectorAll('.luminaires-img').forEach(function (img, i) {
    gsap.to(img, {
      opacity: 1,
      y: 0,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.luminaires',
        start: 'top ' + (75 - i * 10) + '%',
        toggleActions: 'play none none reverse',
      }
    });
  });

  // 3. Vaisselle — images reveal au scroll
  document.querySelectorAll('.vaisselle-img').forEach(function (img, i) {
    gsap.to(img, {
      opacity: 1,
      y: 0,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.vaisselle',
        start: 'top ' + (75 - i * 10) + '%',
        toggleActions: 'play none none reverse',
      }
    });
  });

})();
