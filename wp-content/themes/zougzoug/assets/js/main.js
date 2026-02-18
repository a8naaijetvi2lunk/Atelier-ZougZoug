/* ============================================================
   Atelier ZougZoug — JavaScript global
   - Header scroll behavior
   - Burger menu mobile
   ============================================================ */

(function () {
  'use strict';

  var isMobile = window.matchMedia('(max-width: 768px)').matches;

  // --- Header : transparent → blanc au scroll ---
  (function () {
    var header = document.querySelector('.site-header');
    var spacer = document.querySelector('.hero-spacer') || document.querySelector('.about-hero-spacer');
    var hero = document.querySelector('.hero') || document.querySelector('.about-hero');
    var ticking = false;

    // Si pas de hero spacer (pages autres que l'accueil et à propos), forcer le header scrolled
    if (!spacer) {
      if (header) header.classList.add('scrolled');
      return;
    }

    function onScroll() {
      var scrollY = window.pageYOffset;
      var threshold = isMobile ? spacer.offsetHeight - 64 : spacer.offsetHeight - 80;
      if (scrollY > threshold) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      // Masquer le hero fixe quand on l'a depasse
      if (hero) {
        if (scrollY > spacer.offsetHeight + 100) {
          hero.style.visibility = 'hidden';
        } else {
          hero.style.visibility = 'visible';
        }
      }
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(onScroll);
        ticking = true;
      }
    });
  })();

  // --- Burger menu mobile ---
  (function () {
    var burger = document.querySelector('.burger');
    var mobileNav = document.querySelector('.mobile-nav');
    var header = document.querySelector('.site-header');
    if (!burger || !mobileNav) return;

    burger.addEventListener('click', function () {
      var isOpen = burger.classList.toggle('is-open');
      mobileNav.classList.toggle('is-open', isOpen);
      header.classList.toggle('menu-open', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      mobileNav.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Fermer le menu quand on clique un lien
    mobileNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        burger.classList.remove('is-open');
        mobileNav.classList.remove('is-open');
        header.classList.remove('menu-open');
        burger.setAttribute('aria-expanded', 'false');
        mobileNav.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      });
    });
  })();

})();
