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
      // Nature du projet
      if (d.nature) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Nature du projet</span>';
        html += '<div class="lightbox-detail-value">' + d.nature + '</div>';
        html += '</div>';
      }

      // Client + Adresse
      if (d.client || d.location) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Adresse</span>';
        if (d.client) html += '<p class="lightbox-detail-value lightbox-detail-value--strong">' + escapeAttr(d.client) + '</p>';
        if (d.location) html += '<p class="lightbox-detail-value">' + escapeAttr(d.location) + '</p>';
        if (d.instagram) html += '<p class="lightbox-detail-value"><a href="https://instagram.com/' + escapeAttr(d.instagram.replace('@', '')) + '" target="_blank" rel="noopener">' + escapeAttr(d.instagram) + '</a></p>';
        if (d.website) html += '<p class="lightbox-detail-value"><a href="' + escapeAttr(d.website) + '" target="_blank" rel="noopener">' + escapeAttr(d.website.replace(/^https?:\/\//, '').replace(/\/$/, '')) + '</a></p>';
        html += '</div>';
      }

      // Matériaux
      if (d.materiaux) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Mat\u00e9riaux</span>';
        html += '<div class="lightbox-detail-value">' + d.materiaux + '</div>';
        html += '</div>';
      }

      // Année
      if (d.year) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Ann\u00e9e de r\u00e9alisation</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.year) + '</p>';
        html += '</div>';
      }

      // Collaborateur / Crédits
      if (d.collaborateur) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Collaborateurs</span>';
        html += '<div class="lightbox-detail-value">' + d.collaborateur + '</div>';
        html += '</div>';
      }

      // Description longue
      if (d.texte) {
        html += '<div class="lightbox-sidebar-text">' + d.texte + '</div>';
      }

      if (!d.nature && !d.location && !d.year && !d.texte) {
        html += '<p class="lightbox-placeholder">D\u00e9tails \u00e0 compl\u00e9ter \u2014 ' + escapeAttr(p.name) + '</p>';
      }
    } else {
      html += '<p class="lightbox-placeholder">D\u00e9tails \u00e0 compl\u00e9ter \u2014 ' + escapeAttr(p.name) + '</p>';
    }

    return html;
  }

  /**
   * Masonry layout — round-robin (DOM order = visual order)
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

      /* Round-robin : carte i va dans colonne i % cols */
      var col = i % cols;

      var x = gap + col * (colWidth + gap);
      var y = colHeights[col] + gap;

      item.style.position = 'absolute';
      item.style.left = x + 'px';
      item.style.top = y + 'px';
      item.style.width = colWidth + 'px';

      // Get dimensions: forced orientation or natural ratio
      var img = item.querySelector('img');
      var video = item.querySelector('video');
      var orient = item.getAttribute('data-orientation');
      var itemHeight;
      if (orient === 'portrait') {
        itemHeight = colWidth * (4 / 3);
      } else if (orient === 'landscape') {
        itemHeight = colWidth * (3 / 4);
      } else if (orient === 'square') {
        itemHeight = colWidth;
      } else if (img && img.naturalWidth && img.naturalHeight) {
        itemHeight = colWidth * (img.naturalHeight / img.naturalWidth);
      } else if (video) {
        // Use poster image dimensions (pre-loaded)
        var posterUrl = video.getAttribute('poster');
        var cachedPoster = posterUrl ? layoutMasonry._posterCache && layoutMasonry._posterCache[posterUrl] : null;
        if (cachedPoster && cachedPoster.width && cachedPoster.height) {
          itemHeight = colWidth * (cachedPoster.height / cachedPoster.width);
        } else {
          // Fallback: 16:9 ratio
          itemHeight = colWidth * (9 / 16);
        }
      } else {
        itemHeight = item.offsetHeight || colWidth;
      }

      /* Poser la hauteur si orientation forcee (pour object-fit: cover) */
      if (orient) {
        item.style.height = itemHeight + 'px';
      } else {
        item.style.height = '';
      }

      colHeights[col] = y + itemHeight;
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
    var orients = p.orientations || [];
    for (var i = 0; i < p.medias.length; i++) {
      var file = p.medias[i];
      var src = mediaPath(file);
      var orient = orients[i] || 'auto';
      var orientAttr = orient !== 'auto' ? ' data-orientation="' + orient + '"' : '';
      if (isVideo(file)) {
        var posterSrc = src.replace(/\.(mp4|webm|mov)$/i, '-poster.jpg');
        html += '<div class="lightbox-media lightbox-media--video"' + orientAttr + '>'
          + '<div class="lightbox-video-wrap">'
          + '<video preload="none" playsinline poster="' + posterSrc + '"><source src="' + src + '" type="video/mp4"></video>'
          + '<button class="lightbox-video-play" aria-label="Lire la vid\u00e9o"><svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></button>'
          + '</div>'
          + '</div>';
      } else {
        html += '<div class="lightbox-media"' + orientAttr + '>'
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

    // Wait for images + video posters to load then layout masonry
    var images = lbGrid.querySelectorAll('img');
    var videos = lbGrid.querySelectorAll('video[poster]');
    var loaded = 0;
    var total = images.length + videos.length;
    var layoutDone = false;

    function finishLayout() {
      if (layoutDone) return;
      layoutDone = true;
      layoutMasonry();
      var loader = lbGrid.querySelector('.lightbox-loader');
      if (loader) loader.remove();
      lbGrid.classList.remove('is-loading');
      lbGrid.classList.add('is-ready');
    }

    if (total === 0) {
      finishLayout();
      return;
    }

    function onMediaLoad() {
      loaded++;
      if (loaded >= total) {
        finishLayout();
      }
    }

    for (var j = 0; j < images.length; j++) {
      if (images[j].complete) {
        onMediaLoad();
      } else {
        images[j].addEventListener('load', onMediaLoad);
        images[j].addEventListener('error', onMediaLoad);
      }
    }

    // Pour les vidéos avec poster, charger le poster comme image pour obtenir les dimensions
    if (!layoutMasonry._posterCache) layoutMasonry._posterCache = {};
    for (var v = 0; v < videos.length; v++) {
      (function(videoEl) {
        var posterUrl = videoEl.getAttribute('poster');
        var posterImg = new Image();
        posterImg.onload = function() {
          layoutMasonry._posterCache[posterUrl] = { width: posterImg.naturalWidth, height: posterImg.naturalHeight };
          onMediaLoad();
        };
        posterImg.onerror = onMediaLoad;
        posterImg.src = posterUrl;
      })(videos[v]);
    }

    // Fallback: layout after 3s even if media hasn't loaded
    setTimeout(finishLayout, 3000);
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    var videos = lbGrid.querySelectorAll('video');
    for (var i = 0; i < videos.length; i++) {
      videos[i].pause();
      videos[i].removeAttribute('controls');
      var wrap = videos[i].closest('.lightbox-video-wrap');
      if (wrap) wrap.classList.remove('is-playing');
    }

    lbGrid.classList.remove('is-loading', 'is-ready');
  }

  // Play button on videos
  lbGrid.addEventListener('click', function (e) {
    var btn = e.target.closest('.lightbox-video-play');
    if (!btn) return;
    e.preventDefault();
    var wrap = btn.closest('.lightbox-video-wrap');
    var video = wrap ? wrap.querySelector('video') : null;
    if (!video) return;
    video.setAttribute('controls', '');
    video.play();
    wrap.classList.add('is-playing');
  });

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
