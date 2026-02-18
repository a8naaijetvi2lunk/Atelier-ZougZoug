/* ============================================================
   Atelier ZougZoug — Page Projets
   - Config des 13 projets (medias, descriptions)
   - Generation dynamique des cards
   - Filtres categorie
   - Lightbox galerie plein ecran
   - GSAP ScrollTrigger reveal
   ============================================================ */

var isMobile = window.matchMedia('(max-width: 768px)').matches;

gsap.registerPlugin(ScrollTrigger);


/* ============================================================
   1. DONNEES DES PROJETS
   ============================================================ */

var PROJETS = [
  {
    id: 'becquetance',
    name: 'Becquetance',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'tall',
    desc: "Neo-bistrot dedie aux vins natures, Paris 20e. 150 pieces — 8 modeles en gres chamotte, emaux artisanaux. 2021.",
    folder: 'becquetance',
    cover: 'DSCF3613.webp',
    details: {
      nature: "Set d'assiettes, bols et verseuses : 150 pieces de vaisselle de service, 8 modeles differents.\nPots a couverts : Trio de pots a couverts.",
      adresse: "67 Rue de Menilmontant, 75020 Paris",
      instagram: "@becquetance_paris",
      materiaux: "Gres noir et blanc chamottes, gres pyrite, emaux artisanaux de haute temperature.\nTournage et emaillage a la main.",
      annee: "2021",
      texte: "Creation sur mesure des formes et des couleurs d'un service complet d'assiettes, bols et petites verseuses pour l'ouverture d'un neo-bistrot dedie aux vins natures.\nHuit formats concus et proteges en collaboration avec la cheffe et son associe.\nTerres et emaux developpes specifiquement pour s'integrer a l'identite visuelle et architecturale du lieu."
    },
    medias: [
      'DSCF3613.webp','DSCF3624-r.webp','DSCF3667.webp','DSCF3668.webp','DSCF3681.webp',
      'downloadgram.org_269877960_620223445689368_3036371776655481487_n.webp',
      'downloadgram.org_269878437_115744217469727_3292068785033428973_n.webp',
      'downloadgram.org_269888768_141442848242651_7679837260499242259_n.webp',
      'downloadgram.org_274599237_153171277076956_719021004122873990_n.webp',
      'downloadgram.org_274777683_662625885156758_2440117904076109296_n.webp',
      'downloadgram.org_274839839_483172770125836_4603103867106299023_n.webp',
      'IMG_9677.webp','IMG_9678.webp'
    ]
  },
  {
    id: 'benoit-castel',
    name: 'Benoit Castel',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'wide',
    desc: "Assiettes a brunch co-signees — 30 pieces, gres blanc chamotte. Paris 20e. 2022.",
    folder: 'benoit-castel',
    cover: '217-_J0A0441.webp',
    details: {
      nature: "Set d'assiettes : 30 pieces d'assiettes a brunch.",
      adresse: "11 rue Sorbier 75020 Paris",
      instagram: "@benoitcastel",
      materiaux: "Gres blanc chamotte, email satine de haute temperature.\nTournage et emaillage a la main.",
      annee: "2022",
      texte: "Modele d'assiette co-signee avec le logo/emporte piece emblematique de ces patisseries-boulangeries. A retrouver a la table de la toute derniere adresse de Benoit Castel dans laquelle il devoile sa version tres personnelle du Coffee Shop a la francaise."
    },
    medias: [
      '217-_J0A0441.webp',
      'downloadgram.org_275311568_482117150225384_3332949142901984635_n.webp',
      'IMG_1832.webp','IMG_1837.webp','IMG_1956.webp',
      'downloadgram.org_AQPFdw8xsbqq8qP1zWhFSR_OIhV2Bbf1coe1m7VLlmBaOwiSfAqxl3svQzJ4AkJ7dD36DLHLZSgEsnXpTlbQkvKu4aiF_Xr7Z7yueq8.mp4'
    ]
  },
  {
    id: 'creme-table',
    name: 'Creme',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'square',
    desc: "90 pieces de vaisselle de service — 3 modeles. Assiettes et bols sur mesure.",
    folder: 'creme-table',
    cover: 'DSCF6069.webp',
    details: {
      nature: "Set d'assiettes et bols : 90 pieces de vaisselle de service, 3 modeles differents.",
      adresse: null, instagram: null, materiaux: null, annee: null, texte: null
    },
    medias: [
      'DSCF6069.webp','DSCF6074.webp','DSCF6077.webp','DSCF6080.webp','DSCF6091.webp',
      'CA0CB4F7-E7AB-4EBE-B89E-57A580901EAB.webp',
      'downloadgram.org_278342992_163511689375525_5306329141465571146_n.webp',
      'IMG_4402 2.webp','IMG_4420.webp','IMG_4421.webp','IMG_4424.webp',
      'CREME-email-assiettes.mp4','CREME-proposition-prototypes.mp4','CREME-tournage-assiettes.mp4'
    ]
  },
  {
    id: 'maison-fragile',
    name: 'Maison Fragile',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'tall',
    desc: "Collaboration avec la maison Fragile — pieces en ceramique artisanale.",
    folder: 'maison-fragile',
    details: null,
    cover: 'downloadgram.org_278112606_139870021900017_2569186848507632771_n.webp',
    medias: [
      'downloadgram.org_278112606_139870021900017_2569186848507632771_n.webp',
      'downloadgram.org_278133694_692956411827525_4348149551125668260_n.webp',
      'downloadgram.org_278167726_683434382887994_5082999112652012459_n.webp',
      'downloadgram.org_278349632_144242204779161_7011056578157915950_n.webp',
      'downloadgram.org_278377937_695546144963527_6780521512057319355_n.webp'
    ]
  },
  {
    id: 'petite-marmelade',
    name: 'Petite Marmelade',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'wide',
    desc: "Vaisselle artisanale pour Petite Marmelade — boulangerie et patisserie.",
    folder: 'petite-marmelade',
    details: null,
    cover: 'downloadgram.org_278183442_163766626089792_5475291044375204893_n.webp',
    medias: [
      'downloadgram.org_278183442_163766626089792_5475291044375204893_n.webp',
      'downloadgram.org_278226924_1060796477835505_7990820990676377329_n.webp',
      'downloadgram.org_278232310_120994683872668_684337859033220325_n.webp',
      'downloadgram.org_278287079_293641392973312_1554758205585481807_n.webp',
      'downloadgram.org_278289491_3341579206123170_6905602061411080257_n.webp',
      'downloadgram.org_278386459_3165055947044008_5123505100292474_n.webp',
      'downloadgram.org_278469068_512459050587497_7733611645726987305_n.webp',
      'La Babka -  grand mere  en polonais. Une brioche sans oeuf, avec une texture particuliere, on pe (3).webp',
      'La Babka -  grand mere  en polonais. Une brioche sans oeuf, avec une texture particuliere, on pe.webp',
      'Petite Marmelade sur le marche de Bailly.  Nous serons la tous les week-ends. Merci pour votre a.webp'
    ]
  },
  {
    id: 'pilos',
    name: "Pilo's",
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'square',
    desc: "Vaisselle sur mesure pour le restaurant Pilo's.",
    folder: 'pilos',
    details: null,
    cover: '412645938_18314908273185983_7989985121437337618_n.webp',
    medias: [
      '412645938_18314908273185983_7989985121437337618_n.webp',
      '529198980_18394179391185983_3871492673526886026_n.webp',
      'downloadgram.org_307107129_414228080769927_879674985408518752_n.webp',
      'downloadgram.org_307271850_470658438277100_8190698992983714980_n.webp',
      'downloadgram.org_307306848_456348879887106_2932342839040979089_n.webp'
    ]
  },
  {
    id: 'verre-a-pied',
    name: 'Verre a Pied',
    category: 'table',
    catLabel: 'Art de la Table',
    shape: 'tall',
    desc: "Coupes, verres et pichets en ceramique — pieces uniques tournees a la main.",
    folder: 'verre-a-pied',
    details: null,
    cover: 'trio-verre-pichet-gargoulette_2.webp',
    medias: [
      'trio-verre-pichet-gargoulette_2.webp',
      'coupe-verre-1.webp','coupe-verre-2.webp',
      'fete-1.webp','fete-2.webp','fete-3.webp',
      'clay-market-1.webp','clay-market-2.webp','clay-market-3.webp',
      'verre-video-1.mp4','verre-video-2.mp4'
    ]
  },
  {
    id: 'adele',
    name: 'Adele',
    category: 'luminaires',
    catLabel: 'Luminaires',
    shape: 'tall',
    desc: "Collection de lampes a poser en ceramique — formes sculpturales, finitions mates.",
    folder: 'adele',
    details: null,
    cover: 'Lampe-01.webp',
    medias: [
      'Lampe-01.webp','Lampe-02-1.webp','Lampe-03.webp','Lampe-04.webp','Lampe-05.webp',
      'Lampe-06.webp','Lampe-07.webp','Lampe-08.webp','Lampe-09.webp',
      'downloadgram.org_318260502_2195010077338371_6055511546855892423_n.webp',
      'La boutique en ligne s est refait une fraicheur.webp'
    ]
  },
  {
    id: 'creme-luminaires',
    name: 'Creme — Luminaires',
    category: 'luminaires',
    catLabel: 'Luminaires',
    shape: 'wide',
    desc: "Luminaires sur mesure en ceramique pour Creme — suspensions et appliques.",
    folder: 'creme-luminaires',
    details: null,
    cover: 'downloadgram.org_246023987_974080243487790_4370924502546696389_n.webp',
    medias: [
      'downloadgram.org_246023987_974080243487790_4370924502546696389_n.webp',
      'downloadgram.org_246524759_1027256991443448_5315601361393949503_n.webp',
      'downloadgram.org_246533635_286159300027685_2623138344812117477_n.webp',
      'downloadgram.org_246655977_836252637062664_6648278962588396934_n.webp',
      'downloadgram.org_246795442_4733866956623943_4726270182518974905_n.webp',
      'downloadgram.org_247031921_400068685156543_6581474505444348177_n.webp',
      'downloadgram.org_247039914_948072805804464_2995768135556174987_n.webp',
      'downloadgram.org_247080272_835913547098414_5645448423898254945_n.webp',
      'downloadgram.org_274905528_1350751368726386_2008200217404557015_n.webp',
      'downloadgram.org_274966698_1594616027592442_4943759295318654_n.webp',
      'downloadgram.org_277977131_1014215875868053_2902263390976114550_n.webp',
      'downloadgram.org_278002422_158094919962767_942882727081660497_n.webp',
      'downloadgram.org_278012736_121741367128333_759898904684442410_n.webp',
      'downloadgram.org_278128178_304424378481282_1759763784188816268_n.webp',
      'downloadgram.org_AQMeZ5mKdG_0Kruyvfk9IzIP3XCO2wrVvNe16G-a0o9OpWF4LcCy_p5KKK6QvvvVbe9d5ZwvJYfwjGvaU9iN14yPRCfxeYnmC-DhK_I.mp4'
    ]
  },
  {
    id: 'padam',
    name: 'Padam Hotel / Artefak',
    category: 'luminaires',
    catLabel: 'Luminaires',
    shape: 'square',
    desc: "Luminaires sur mesure pour l'hotel Le Padam — en collaboration avec Artefak.",
    folder: 'padam',
    details: null,
    cover: '678fa52ec38497f293f81d20_20240412-Edith@SimonDetraz-146.webp',
    medias: [
      '678fa52ec38497f293f81d20_20240412-Edith@SimonDetraz-146.webp',
      'hotel-le-padam-chambres-173470-2400-2400-auto.webp',
      '15.webp','16.webp','17.webp',
      "Capture d'ecran 2023-06-21 a 12.00.23.png",
      "Capture d'ecran 2023-06-21 a 12.00.33.png",
      "Capture d'ecran 2023-06-21 a 12.00.45.png",
      'IMG_7109.webp',
      'downloadgram.org_AQM6Kgr_Li3hwcpiZKFjnIkV1D5zBWjJdTndU5iOIv9VWETGhkm0j9BwAsys0hSQrPZNaHSvJ7B8S9DqiQ8LO4Kh.mp4',
      'PADAM_video-process.mp4'
    ]
  },
  {
    id: 'sella',
    name: 'Sella St Barth',
    category: 'luminaires',
    catLabel: 'Luminaires',
    shape: 'wide',
    desc: "Luminaires en ceramique pour Sella, St Barthelemy.",
    folder: 'sella',
    details: null,
    cover: 'downloadgram.org_303222843_1094195011457419_847084932020638827_n.webp',
    medias: [
      'downloadgram.org_303222843_1094195011457419_847084932020638827_n.webp',
      'downloadgram.org_303314357_3339080109700925_1349332588085582290_n.webp',
      'downloadgram.org_304749384_1559345817828781_8285200381504553_n.webp',
      'downloadgram.org_304916644_932748734781962_1064384184609911015_n.webp',
      'downloadgram.org_AQPft-vEKHe698XxLTJc9-EizAwJ0c0dmM-bXeQPjyopUYgyoUCUYUHzi3VGCy5UCJVlm1l5_mDGFg1yu6ZJ44-WKehU70aZ8xSCzK4.mp4'
    ]
  },
  {
    id: 'carhartt',
    name: 'Carhartt WIP',
    category: 'accessoires',
    catLabel: 'Accessoires',
    shape: 'wide',
    desc: "Collaboration avec Carhartt WIP — pieces en ceramique en edition limitee.",
    folder: 'carhartt',
    details: null,
    cover: 'DSCF4348.webp',
    medias: [
      'DSCF4348.webp','DSCF4355.webp','DSCF4385.webp',
      'downloadgram.org_259833148_2791308511167393_780146704438046472_n.webp'
    ]
  },
  {
    id: 'conscience-parfums',
    name: 'Conscience Parfums',
    category: 'accessoires',
    catLabel: 'Accessoires',
    shape: 'tall',
    desc: "Brule-parfums en ceramique artisanale pour Conscience Parfums.",
    folder: 'conscience-parfums',
    details: null,
    cover: 'downloadgram.org_316121412_658212345903385_1973795775866386581_n.webp',
    medias: [
      'downloadgram.org_316121412_658212345903385_1973795775866386581_n.webp',
      'downloadgram.org_316492279_2351276768361833_7397725631778115059_n.webp',
      'downloadgram.org_316513660_3264108260505191_7294030422919262910_n.webp',
      'downloadgram.org_316579918_3322300788031820_4210834688253571744_n.webp',
      'downloadgram.org_316597716_3248452098734910_8257732803228761756_n.webp',
      'downloadgram.org_316747418_3196936940557976_7578205646431124361_n.webp',
      '4f68ce_9399741e06e849a28f0fe39f34e89005~mv2.avif',
      'cf187b_55c3766075f1420f89160dd2726204ec~mv2.avif',
      'downloadgram.org_AQN_X0kXfwI8mDjKdL7pFnzaNHXPLb6gVymKpPP0AkSa8mbCruU4XQgBs6NnbpjQuP-XGzsNQ8KHkRUMyS8BqlZ2.mp4'
    ]
  }
];


/* ============================================================
   2. HELPERS
   ============================================================ */

function isVideo(filename) {
  return /\.(mp4|webm|mov)$/i.test(filename);
}

function mediaPath(folder, file) {
  var base = (typeof zzProjets !== 'undefined' && zzProjets.imgBase) ? zzProjets.imgBase : '';
  return base + 'projets/' + encodeURIComponent(folder) + '/' + encodeURIComponent(file);
}

function hasVideos(projet) {
  for (var i = 0; i < projet.medias.length; i++) {
    if (isVideo(projet.medias[i])) return true;
  }
  return false;
}

function escapeAttr(str) {
  return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;');
}


/* ============================================================
   3. GENERER LES CARDS
   ============================================================ */

(function () {
  var grid = document.getElementById('projets-grid');
  if (!grid) return;

  var html = '';
  for (var i = 0; i < PROJETS.length; i++) {
    var p = PROJETS[i];
    var coverSrc = mediaPath(p.folder, p.cover);
    var badge = '';
    if (hasVideos(p)) {
      badge = '<div class="projet-card-badge"><svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></div>';
    }
    html += '<div class="projet-card" data-category="' + p.category + '" data-index="' + i + '">'
      + '<img src="' + coverSrc + '" alt="' + escapeAttr(p.name) + ' — ' + escapeAttr(p.catLabel) + '" loading="lazy">'
      + badge
      + '<div class="projet-card-overlay">'
      + '<span class="projet-card-name">' + p.name + '</span>'
      + '<span class="projet-card-cat">' + p.catLabel + '</span>'
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
   5. LIGHTBOX
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

    if (p.details) {
      var d = p.details;
      if (d.nature) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Nature du projet</span>';
        html += '<p class="lightbox-detail-value">' + nl2br(escapeAttr(d.nature)) + '</p>';
        html += '</div>';
      }
      if (d.adresse) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Adresse</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.adresse) + '</p>';
        html += '</div>';
      }
      if (d.instagram) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Instagram</span>';
        html += '<p class="lightbox-detail-value"><a href="https://www.instagram.com/' + escapeAttr(d.instagram.replace('@', '')) + '/" target="_blank" rel="noopener">' + escapeAttr(d.instagram) + '</a></p>';
        html += '</div>';
      }
      if (d.materiaux) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Materiaux</span>';
        html += '<p class="lightbox-detail-value">' + nl2br(escapeAttr(d.materiaux)) + '</p>';
        html += '</div>';
      }
      if (d.annee) {
        html += '<div class="lightbox-detail">';
        html += '<span class="lightbox-detail-label">Annee</span>';
        html += '<p class="lightbox-detail-value">' + escapeAttr(d.annee) + '</p>';
        html += '</div>';
      }
      if (d.texte) {
        html += '<div class="lightbox-sidebar-text">' + nl2br(escapeAttr(d.texte)) + '</div>';
      }
      var hasFullDetails = d.adresse || d.materiaux || d.annee || d.texte;
      if (!hasFullDetails && d.nature) {
        html += '<p class="lightbox-placeholder">Details a completer — ' + escapeAttr(p.name) + '</p>';
      }
    } else {
      html += '<p class="lightbox-placeholder">Details a completer — ' + escapeAttr(p.name) + '</p>';
    }

    return html;
  }

  function openLightbox(index) {
    var p = PROJETS[index];
    if (!p) return;

    lbSidebar.innerHTML = buildSidebar(p);

    var html = '';
    for (var i = 0; i < p.medias.length; i++) {
      var file = p.medias[i];
      var src = mediaPath(p.folder, file);
      if (isVideo(file)) {
        html += '<div class="lightbox-media lightbox-media--wide">'
          + '<video controls preload="none" playsinline><source src="' + src + '" type="video/mp4"></video>'
          + '</div>';
      } else {
        html += '<div class="lightbox-media">'
          + '<img src="' + src + '" alt="' + escapeAttr(p.name) + '" loading="lazy">'
          + '</div>';
      }
    }
    lbGrid.innerHTML = html;

    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    lbSidebar.scrollTop = 0;
    lbGrid.scrollTop = 0;
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    var videos = lbGrid.querySelectorAll('video');
    for (var i = 0; i < videos.length; i++) {
      videos[i].pause();
    }
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
