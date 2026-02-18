/* ============================================================
   Atelier ZougZoug — Dashboard JS
   Animations subtiles au chargement
   ============================================================ */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // Reveal progressif des elements
    var items = document.querySelectorAll('.zz-dash-shortcut, .zz-dash-card');
    for (var i = 0; i < items.length; i++) {
      items[i].style.opacity = '0';
      items[i].style.transform = 'translateY(12px)';
      items[i].style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    }

    requestAnimationFrame(function () {
      for (var i = 0; i < items.length; i++) {
        (function (el, delay) {
          setTimeout(function () {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
          }, delay);
        })(items[i], 60 + i * 60);
      }
    });
  });
})();
