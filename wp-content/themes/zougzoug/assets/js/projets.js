/* ============================================================
   Atelier ZougZoug — Page Projets
   - Donnees CPT (via wp_localize_script) ou fallback hardcode
   - Generation dynamique des cards
   - Filtres categorie
   - Lightbox galerie plein ecran — Masonry calcule
   - GSAP ScrollTrigger reveal
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);


/* ============================================================
   1. DONNEES DES PROJETS (CPT via zzProjets.projets)
   ============================================================ */

var PROJETS = (typeof zzProjets !== 'undefined' && zzProjets.projets && zzProjets.projets.length > 0)
  ? zzProjets.projets
  : [];


/* ============================================================
   2. HELPERS
   ============================================================ */

function isVideo(filename) {
  return /\.(mp4|webm|mov)$/i.test(filename);
}

function mediaPath(file) {
  if (!file) return '';
  // If already a full URL
  if (file.indexOf('http') === 0 || file.indexOf('//') === 0) return file;
  var base = (typeof zzProjets !== 'undefined' && zzProjets.imgBase) ? zzProjets.imgBase : '';
  return base + 'projets/' + file.split('/').map(encodeURIComponent).join('/');
}

function hasVideos(projet) {
  for (var i = 0; i < projet.medias.length; i++) {
    if (isVideo(projet.medias[i])) return true;
  }
  return false;
}

function escapeAttr(str) {
  if (!str) return '';
  return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;');
}


/* ============================================================
   3. GENERER LES CARDS
   ============================================================ */

(function () {
  var grid = document.getElementById('projets-grid');
  if (!grid || PROJETS.length === 0) return;

  var html = '';
  for (var i = 0; i < PROJETS.length; i++) {
    var p = PROJETS[i];
    var coverSrc = mediaPath(p.cover);
    var badge = '';
    if (hasVideos(p)) {
      badge = '<div class="projet-card-badge"><svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>';
    }
    html += '<div class="projet-card" data-category="' + escapeAttr(p.category) + '" data-index="' + i + '">'
      + '<img src="' + coverSrc + '" alt="' + escapeAttr(p.name) + ' — ' + escapeAttr(p.catLabel) + '" loading="lazy">'
      + badge
      + '<div class="projet-card-overlay">'
      + '<span class="projet-card-name">' + escapeAttr(p.name) + '</span>'
      + '<span class="projet-card-cat">' + escapeAttr(p.catLabel) + '</span>'
      + '</div>'
      + '</div>';
  }
  grid.innerHTML = html;
})();


/* ============================================================
   4. FILTRES CATEGORIE
   ============================================================ */

(function () {
  var buttons = document.querySelectorAll('.filter-btn');

  function filterCards(cat) {
    var cards = document.querySelectorAll('.projet-card');
    for (var i = 0; i < cards.length; i++) {
      var card = cards[i];
      var shouldShow = (cat === 'all' || card.getAttribute('data-category') === cat);

      gsap.killTweensOf(card);

      if (shouldShow) {
        card.classList.remove('is-hidden');
        gsap.fromTo(card,
          { opacity: 0, scale: 0.95 },
          { opacity: 1, scale: 1, duration: 0.35, delay: i * 0.04, ease: 'power2.out' }
        );
      } else {
        card.classList.add('is-hidden');
      }
    }
    setTimeout(function () { ScrollTrigger.refresh(); }, 400);
  }

  for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', function () {
      for (var j = 0; j < buttons.length; j++) {
        buttons[j].classList.remove('is-active');
      }
      this.classList.add('is-active');
      filterCards(this.getAttribute('data-filter'));
    });
  }
})();


/* ============================================================
   5. LIGHTBOX — Masonry calcule
   ============================================================ */

(function () {
  var lightbox = document.getElementById('lightbox');
  var lbSidebar = document.getElementById('lightbox-sidebar');
  var lbGrid = document.getElementById('lightbox-grid');
  if (!lightbox) return;

  function nl2br(str) {
    return str.replace(/\n/g, '<br>');
  }

  function buildSidebar(p) {
    var html = '';
    html += '<h2 class="lightbox-sidebar-name">' + escapeAttr(p.name) + '</h2>';
    html += '<p class="lightbox-sidebar-cat">' + escapeAttr(p.catLabel) + '</p>';

    var d = p.details;
    if (d) {
      if (d.location) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Adresse</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.location) + '</p>';
        html += '</div>';
      }
      if (d.year) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Annee</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.year) + '</p>';
        html += '</div>';
      }
      if (d.client) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Client</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.client) + '</p>';
        html += '</div>';
      }
      if (d.texte) {
        html += '<div class="lightbox-sidebar-text">' + nl2br(escapeAttr(d.texte)) + '</div>';
      }
      if (!d.location && !d.year && !d.texte) {
        html += '<p class="lightbox-placeholder">Details a completer — ' + escapeAttr(p.name) + '</p>';
      }
    } else {
      html += '<p class="lightbox-placeholder">Details a completer — ' + escapeAttr(p.name) + '</p>';
    }

    return html;
  }

  /**
   * Masonry layout — place each image in the shortest column
   * Waits for all images to load to get natural dimensions
   */
  function layoutMasonry() {
    var cols = isMobile ? 2 : 3;
    var gap = isMobile ? 8 : 10;

    // Get or create the inner masonry wrapper
    var masonryWrap = lbGrid.querySelector('.lightbox-masonry');
    if (!masonryWrap) return;

    var items = masonryWrap.querySelectorAll('.lightbox-media');
    if (!items.length) return;

    masonryWrap.style.position = 'relative';

    var containerWidth = lbGrid.clientWidth - (gap * 2);
    var colWidth = (containerWidth - gap * (cols - 1)) / cols;
    var colHeights = [];
    for (var c = 0; c < cols; c++) colHeights.push(0);

    for (var i = 0; i < items.length; i++) {
      var item = items[i];
      var isWide = item.classList.contains('lightbox-media--wide');

      if (isWide) {
        var maxHeight = Math.max.apply(null, colHeights);
        item.style.position = 'absolute';
        item.style.left = gap + 'px';
        item.style.top = maxHeight + gap + 'px';
        item.style.width = containerWidth + 'px';
        var videoHeight = item.offsetHeight;
        for (var vc = 0; vc < cols; vc++) {
          colHeights[vc] = maxHeight + gap + videoHeight;
        }
      } else {
        var shortest = 0;
        for (var sc = 1; sc < cols; sc++) {
          if (colHeights[sc] < colHeights[shortest]) shortest = sc;
        }

        var x = gap + shortest * (colWidth + gap);
        var y = colHeights[shortest] + gap;

        item.style.position = 'absolute';
        item.style.left = x + 'px';
        item.style.top = y + 'px';
        item.style.width = colWidth + 'px';

        var img = item.querySelector('img');
        var itemHeight;
        if (img && img.naturalWidth && img.naturalHeight) {
          itemHeight = colWidth * (img.naturalHeight / img.naturalWidth);
        } else {
          itemHeight = item.offsetHeight;
        }

        colHeights[shortest] = y + itemHeight;
      }
    }

    // Set height on the inner wrapper, not the scroll container
    var totalHeight = Math.max.apply(null, colHeights) + gap;
    masonryWrap.style.height = totalHeight + 'px';
  }

  function openLightbox(index) {
    var p = PROJETS[index];
    if (!p) return;

    lbSidebar.innerHTML = buildSidebar(p);

    // Build media elements inside a masonry wrapper
    var html = '<div class="lightbox-loader"><div class="lightbox-loader-spinner"></div></div>';
    html += '<div class="lightbox-masonry">';
    for (var i = 0; i < p.medias.length; i++) {
      var file = p.medias[i];
      var src = mediaPath(file);
      if (isVideo(file)) {
        html += '<div class="lightbox-media lightbox-media--wide">'
          + '<video controls preload="none" playsinline><source src="' + src + '" type="video/mp4"></video>'
          + '</div>';
      } else {
        html += '<div class="lightbox-media">'
          + '<img src="' + src + '" alt="' + escapeAttr(p.name) + '">'
          + '</div>';
      }
    }
    html += '</div>';
    lbGrid.innerHTML = html;
    lbGrid.classList.add('is-loading');
    lbGrid.classList.remove('is-ready');

    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    lbSidebar.scrollTop = 0;
    lbGrid.scrollTop = 0;

    // Wait for images to load then layout masonry
    var images = lbGrid.querySelectorAll('img');
    var loaded = 0;
    var total = images.length;
    var layoutDone = false;

    function finishLayout() {
      if (layoutDone) return;
      layoutDone = true;
      layoutMasonry();
      // Remove spinner, reveal images
      var loader = lbGrid.querySelector('.lightbox-loader');
      if (loader) loader.remove();
      lbGrid.classList.remove('is-loading');
      lbGrid.classList.add('is-ready');
    }

    if (total === 0) {
      finishLayout();
      return;
    }

    function onImageLoad() {
      loaded++;
      if (loaded >= total) {
        finishLayout();
      }
    }

    for (var j = 0; j < images.length; j++) {
      if (images[j].complete) {
        onImageLoad();
      } else {
        images[j].addEventListener('load', onImageLoad);
        images[j].addEventListener('error', onImageLoad);
      }
    }

    // Fallback: layout after 3s even if images haven't loaded
    setTimeout(finishLayout, 3000);
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    var videos = lbGrid.querySelectorAll('video');
    for (var i = 0; i < videos.length; i++) {
      videos[i].pause();
    }

    lbGrid.classList.remove('is-loading', 'is-ready');
  }

  document.getElementById('projets-grid').addEventListener('click', function (e) {
    if (lightbox.classList.contains('is-open')) return;
    var card = e.target.closest('.projet-card');
    if (!card) return;
    var index = parseInt(card.getAttribute('data-index'), 10);
    openLightbox(index);
  });

  lightbox.addEventListener('click', function (e) {
    if (e.target.closest('.lightbox-close')) {
      e.preventDefault();
      closeLightbox();
      return;
    }
    if (e.target === lightbox) {
      closeLightbox();
      return;
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
      e.preventDefault();
      closeLightbox();
    }
  });

  // Re-layout on resize
  var resizeTimer;
  window.addEventListener('resize', function () {
    if (!lightbox.classList.contains('is-open')) return;
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      isMobile = window.matchMedia('(max-width: 768px)').matches;
      layoutMasonry();
    }, 200);
  });
})();


/* ============================================================
   6. GSAP ANIMATIONS
   ============================================================ */

// Hero : fade in titre + filtres
(function () {
  var hero = document.querySelector('.projets-hero-inner');
  if (!hero) return;
  var children = hero.children;
  for (var i = 0; i < children.length; i++) {
    gsap.fromTo(children[i],
      { opacity: 0, y: 30 },
      { opacity: 1, y: 0, duration: 0.9, delay: 0.2 + i * 0.12, ease: 'power3.out', clearProps: 'transform' }
    );
  }
})();
