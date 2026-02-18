/* ============================================================
   Atelier ZougZoug — Editeur Split-Screen (Vanilla JS)
   Formulaire dynamique + sauvegarde REST API + iframe preview
   ============================================================ */

(function () {
  'use strict';

  /* --------------------------------------------------------
     SCHEMAS — Definition des champs par page
     -------------------------------------------------------- */

  var SCHEMAS = {
    home: [
      {
        key: 'hero', title: 'Hero Header', icon: 'dashicons-format-image',
        fields: [
          { key: 'tagline', label: 'Accroche', type: 'richtext' },
          { key: 'slides', label: 'Slides', type: 'hero-slides' }
        ]
      },
      {
        key: 'statement', title: 'Statement', icon: 'dashicons-editor-quote',
        fields: [
          { key: 'text', label: 'Texte', type: 'richtext' },
          { key: 'images', label: 'Images', type: 'statement-images' }
        ]
      },
      {
        key: 'showcases', title: 'Vitrines', icon: 'dashicons-images-alt2',
        fields: [],
        repeater: {
          fields: [
            { key: 'label', label: 'Label', type: 'text' },
            { key: 'texts', label: 'Textes', type: 'textarea-array' },
            { key: 'ref_label', label: 'Label references', type: 'text' },
            { key: 'ref_text', label: 'Texte references', type: 'text' },
            { key: 'img_main', label: 'Image principale', type: 'image' },
            { key: 'img_main_alt', label: 'Alt image principale', type: 'text' },
            { key: 'img_secondary', label: 'Image secondaire', type: 'image' },
            { key: 'img_secondary_alt', label: 'Alt image secondaire', type: 'text' },
            { key: 'cta_text', label: 'Texte du bouton', type: 'text' },
            { key: 'cta_url', label: 'URL du bouton', type: 'url' }
          ]
        }
      },
      {
        key: 'instagram', title: 'Galerie', icon: 'dashicons-format-gallery',
        fields: [
          { key: 'url', label: 'Lien Instagram', type: 'url' },
          { key: 'images', label: 'Photos', type: 'gallery' }
        ]
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    about: [
      {
        key: 'hero', title: 'Hero', icon: 'dashicons-admin-users',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'name', label: 'Nom', type: 'text' },
          { key: 'text', label: 'Texte biographie', type: 'richtext' },
          { key: 'portrait', label: 'Portrait', type: 'image' },
          { key: 'portrait_alt', label: 'Alt portrait', type: 'text' }
        ]
      },
      {
        key: 'blocs', title: 'Blocs contenu', icon: 'dashicons-text-page',
        fields: [],
        repeater: {
          fields: [
            { key: 'label', label: 'Label', type: 'text' },
            { key: 'tagline', label: 'Accroche', type: 'text' },
            { key: 'texts', label: 'Textes', type: 'textarea-array' },
            { key: 'image', label: 'Image', type: 'image' },
            { key: 'image_alt', label: 'Alt image', type: 'text' }
          ]
        }
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'richtext' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    contact: [
      {
        key: 'form', title: 'Formulaire', icon: 'dashicons-email',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'title', label: 'Titre', type: 'richtext' }
        ]
      },
      {
        key: 'info', title: 'Infos contact', icon: 'dashicons-location',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'name', label: 'Nom', type: 'text' },
          { key: 'studio', label: 'Studio', type: 'text' },
          { key: 'email', label: 'Email', type: 'text' },
          { key: 'phone', label: 'Telephone', type: 'text' },
          { key: 'address', label: 'Adresse', type: 'richtext' },
          { key: 'instagram_handle', label: 'Instagram', type: 'text' },
          { key: 'photo', label: 'Photo', type: 'image' },
          { key: 'photo_alt', label: 'Alt photo', type: 'text' }
        ]
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'richtext' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    cours: [
      {
        key: 'hero', title: 'Hero', icon: 'dashicons-format-image',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'title', label: 'Titre', type: 'text' },
          { key: 'image', label: 'Image', type: 'image' },
          { key: 'image_alt', label: 'Alt image', type: 'text' }
        ]
      },
      {
        key: 'intro', title: 'Introduction', icon: 'dashicons-editor-quote',
        fields: [
          { key: 'accroche', label: 'Accroche', type: 'text' },
          { key: 'text', label: 'Texte', type: 'richtext' }
        ]
      },
      {
        key: 'offres', title: 'Offres', icon: 'dashicons-tickets-alt',
        fields: [
          { key: 'offres_label', label: 'Label section', type: 'text', root: true },
          { key: 'offres_title', label: 'Titre section', type: 'text', root: true },
          { key: 'privatisation', label: 'Note privatisation', type: 'text', root: true }
        ],
        repeater: {
          fields: [
            { key: 'nom', label: 'Nom', type: 'text' },
            { key: 'description', label: 'Description', type: 'richtext' },
            { key: 'prix', label: 'Prix (€)', type: 'number' },
            { key: 'infos', label: 'Infos (durée, nb personnes…)', type: 'textarea-array' }
          ]
        }
      },
      {
        key: 'wecandoo', title: 'WeCanDoo', icon: 'dashicons-admin-links',
        fields: [
          { key: 'text', label: 'Texte', type: 'text' },
          { key: 'link_text', label: 'Texte du lien', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        key: 'galerie', title: 'Galerie', icon: 'dashicons-format-gallery',
        fields: [
          { key: 'images', label: 'Photos', type: 'gallery' }
        ]
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'richtext' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        key: 'faq', title: 'FAQ', icon: 'dashicons-editor-help',
        fields: [],
        repeater: {
          fields: [
            { key: 'question', label: 'Question', type: 'text' },
            { key: 'answer', label: 'Réponse', type: 'richtext' }
          ]
        }
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    revendeurs: [
      {
        key: 'hero', title: 'Hero', icon: 'dashicons-format-image',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'title', label: 'Titre', type: 'richtext' },
          { key: 'text', label: 'Sous-titre', type: 'text' }
        ]
      },
      {
        key: 'lieux', title: 'Points de vente', icon: 'dashicons-store',
        fields: [
          { key: 'lieux_label', label: 'Label section', type: 'text', root: true },
          { key: 'lieux_title', label: 'Titre section', type: 'text', root: true }
        ],
        repeater: {
          fields: [
            { key: 'type', label: 'Type', type: 'text' },
            { key: 'nom', label: 'Nom', type: 'text' },
            { key: 'adresse', label: 'Adresse', type: 'richtext' },
            { key: 'note', label: 'Note', type: 'text' },
            { key: 'link', label: 'Lien', type: 'url' },
            { key: 'link_text', label: 'Texte du lien', type: 'text' }
          ]
        }
      },
      {
        key: 'photos', title: 'Galerie photos', icon: 'dashicons-format-gallery',
        fields: [
          { key: 'images', label: 'Photos', type: 'gallery' }
        ]
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'richtext' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    collaborations: [
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    mentions: [
      {
        key: 'root', title: 'Informations', icon: 'dashicons-shield',
        fields: [
          { key: 'title', label: 'Titre de la page', type: 'text' },
          { key: 'last_updated', label: 'Dernière mise à jour', type: 'text' }
        ],
        flat: true
      },
      {
        key: 'sections', title: 'Sections', icon: 'dashicons-text-page',
        fields: [],
        repeater: {
          fields: [
            { key: 'title', label: 'Titre de la section', type: 'text' },
            { key: 'content', label: 'Contenu (HTML)', type: 'richtext' }
          ]
        }
      },
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200×630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
    ],

    global: [
      {
        key: 'root', title: 'Informations generales', icon: 'dashicons-admin-settings',
        fields: [
          { key: 'site_title', label: 'Titre du site', type: 'text' },
          { key: 'site_subtitle', label: 'Sous-titre', type: 'text' }
        ],
        flat: true
      },
      {
        key: 'footer', title: 'Footer', icon: 'dashicons-editor-insertmore',
        fields: [
          { key: 'baseline', label: 'Baseline', type: 'text' },
          { key: 'email', label: 'Email', type: 'text' },
          { key: 'phone', label: 'Telephone', type: 'text' },
          { key: 'address', label: 'Adresse', type: 'richtext' },
          { key: 'instagram', label: 'URL Instagram', type: 'url' }
        ]
      },
      {
        key: 'meta', title: 'Meta / SEO', icon: 'dashicons-search',
        fields: [
          { key: 'description', label: 'Meta description', type: 'richtext' },
          { key: 'og_image', label: 'Image OpenGraph', type: 'image' }
        ]
      }
    ]
  };


  /* --------------------------------------------------------
     INIT
     -------------------------------------------------------- */

  var wrap, formEl, iframe, statusEl, pageSlug, pageData;

  document.addEventListener('DOMContentLoaded', function () {
    wrap = document.querySelector('.zz-editor-wrap');
    if (!wrap) return;

    formEl = document.getElementById('zz-editor-form');
    iframe = document.getElementById('zz-editor-iframe');
    statusEl = document.getElementById('zz-editor-status');
    pageSlug = wrap.getAttribute('data-page');

    initResizer();
    loadData();
  });


  /* --------------------------------------------------------
     RESIZABLE SPLIT
     -------------------------------------------------------- */

  function initResizer() {
    var divider = document.getElementById('zz-editor-divider');
    if (!divider) return;

    var startX, startWidth;

    divider.addEventListener('mousedown', function (e) {
      e.preventDefault();
      startX = e.clientX;
      startWidth = formEl.offsetWidth;
      divider.classList.add('is-dragging');
      document.body.style.cursor = 'col-resize';
      document.body.style.userSelect = 'none';

      if (iframe) iframe.style.pointerEvents = 'none';

      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
    });

    function onMouseMove(e) {
      var newWidth = Math.max(300, Math.min(startWidth + (e.clientX - startX), window.innerWidth * 0.6));
      formEl.style.width = newWidth + 'px';
      formEl.style.minWidth = 'auto';
      formEl.style.maxWidth = 'none';
    }

    function onMouseUp() {
      divider.classList.remove('is-dragging');
      document.body.style.cursor = '';
      document.body.style.userSelect = '';

      if (iframe) iframe.style.pointerEvents = '';

      document.removeEventListener('mousemove', onMouseMove);
      document.removeEventListener('mouseup', onMouseUp);
    }
  }


  /* --------------------------------------------------------
     LOAD DATA
     -------------------------------------------------------- */

  function loadData() {
    fetch(zzEditor.restUrl + pageSlug, {
      headers: { 'X-WP-Nonce': zzEditor.nonce }
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      pageData = json.data || {};
      buildForm();
    })
    .catch(function () {
      formEl.innerHTML = '<p style="color:#E53935;padding:20px;">Erreur de chargement.</p>';
    });
  }


  /* --------------------------------------------------------
     BUILD FORM
     -------------------------------------------------------- */

  function buildForm() {
    var schema = SCHEMAS[pageSlug];
    if (!schema) {
      formEl.innerHTML = '<p style="padding:20px;color:rgba(26,26,26,0.4);">Aucun schema defini pour cette page.</p>';
      return;
    }

    var html = '';

    for (var s = 0; s < schema.length; s++) {
      var section = schema[s];
      var isOpen = s === 0 ? ' is-open' : '';

      html += '<div class="zz-section' + isOpen + '" data-section="' + section.key + '">';
      html += '<div class="zz-section-header">';
      html += '<span class="zz-section-title">';
      if (section.icon) html += '<span class="dashicons ' + section.icon + '"></span>';
      html += section.title + '</span>';
      html += '<svg class="zz-section-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>';
      html += '</div>';
      html += '<div class="zz-section-body">';

      // Get section data
      var sectionData;
      if (section.flat) {
        sectionData = pageData;
      } else {
        sectionData = pageData[section.key] || {};
      }

      // Regular fields
      for (var f = 0; f < section.fields.length; f++) {
        var field = section.fields[f];
        var value, path;
        if (field.root) {
          value = pageData[field.key];
          path = field.key;
        } else if (section.flat) {
          value = sectionData[field.key];
          path = field.key;
        } else {
          value = sectionData[field.key];
          path = section.key + '.' + field.key;
        }
        html += buildField(field, value, path);
      }

      // Repeater
      if (section.repeater) {
        var items = Array.isArray(sectionData) ? sectionData : [];
        html += '<div class="zz-repeater" data-path="' + section.key + '">';
        for (var r = 0; r < items.length; r++) {
          html += buildRepeaterItem(section.repeater.fields, items[r], section.key, r);
        }
        html += '<button type="button" class="zz-repeater-add" data-path="' + section.key + '">+ Ajouter</button>';
        html += '</div>';
      }

      html += '</div></div>';
    }

    // Save button
    html += '<div class="zz-save-bar">';
    html += '<button type="button" class="zz-save-btn" id="zz-save-btn">Sauvegarder</button>';
    html += '</div>';

    formEl.innerHTML = html;

    // Bind events
    bindAccordions();
    bindSaveButton();
    bindImagePickers();
    bindRepeaters();
    bindStatementImages();
    bindHeroSlides();
    bindGallery();
    bindSeoCounters();
    initRichTextEditors();
  }


  /* --------------------------------------------------------
     BUILD FIELD
     -------------------------------------------------------- */

  function buildField(field, value, path) {
    var html = '<div class="zz-field" data-path="' + path + '">';
    html += '<label class="zz-field-label">' + escHtml(field.label) + '</label>';

    if (field.type === 'text' || field.type === 'url' || field.type === 'number') {
      var inputType = field.type === 'url' ? 'url' : field.type === 'number' ? 'number' : 'text';
      var val = value != null ? value : '';
      html += '<input type="' + inputType + '" class="zz-input" data-path="' + path + '" value="' + escAttr(String(val)) + '">';
    }

    else if (field.type === 'textarea') {
      var val2 = value != null ? value : '';
      html += '<textarea class="zz-input" data-path="' + path + '" rows="3">' + escHtml(String(val2)) + '</textarea>';
    }

    else if (field.type === 'richtext') {
      var val2r = value != null ? String(value) : '';
      var editorId = 'zz-rich-' + path.replace(/\./g, '-');
      html += '<div class="zz-richtext-wrap" data-path="' + path + '">';
      html += '<textarea id="' + editorId + '" class="zz-input zz-richtext" data-path="' + path + '">' + escHtml(val2r) + '</textarea>';
      html += '</div>';
    }

    else if (field.type === 'seo-text') {
      var val3 = value != null ? value : '';
      var max3 = field.maxLength || 60;
      var len3 = val3.length;
      var cls3 = len3 > max3 ? ' zz-counter--over' : '';
      html += '<input type="text" class="zz-input zz-seo-input" data-path="' + path + '" value="' + escAttr(String(val3)) + '">';
      html += '<span class="zz-counter' + cls3 + '" data-path="' + path + '" data-max="' + max3 + '">' + len3 + '/' + max3 + '</span>';
    }

    else if (field.type === 'seo-textarea') {
      var val4 = value != null ? value : '';
      var max4 = field.maxLength || 160;
      var len4 = val4.length;
      var cls4 = len4 > max4 ? ' zz-counter--over' : '';
      html += '<textarea class="zz-input zz-seo-input" data-path="' + path + '" rows="3">' + escHtml(String(val4)) + '</textarea>';
      html += '<span class="zz-counter' + cls4 + '" data-path="' + path + '" data-max="' + max4 + '">' + len4 + '/' + max4 + '</span>';
    }

    else if (field.type === 'textarea-array') {
      var items = Array.isArray(value) ? value : [];
      for (var i = 0; i < items.length; i++) {
        html += '<textarea class="zz-input zz-input-array" data-path="' + path + '" data-index="' + i + '" rows="3" style="margin-bottom:6px;">' + escHtml(items[i] || '') + '</textarea>';
      }
    }

    else if (field.type === 'image') {
      var imgSrc = value || '';
      html += '<div class="zz-field-image">';
      html += '<div class="zz-img-preview" data-path="' + path + '">';
      if (imgSrc) {
        html += '<img src="' + escAttr(resolveImageUrl(imgSrc)) + '" alt="">';
      } else {
        html += '<div class="zz-img-preview-empty"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
      }
      html += '</div>';
      html += '<input type="hidden" class="zz-input" data-path="' + path + '" value="' + escAttr(imgSrc) + '">';
      html += '<button type="button" class="zz-img-btn" data-path="' + path + '">' + (imgSrc ? 'Changer' : 'Choisir une image') + '</button>';
      html += '</div>';
    }

    else if (field.type === 'statement-images') {
      var images = Array.isArray(value) ? value : [];
      // Get mobile images from pageData
      var sectionKey = path.split('.')[0];
      var mobileImages = (pageData[sectionKey] && Array.isArray(pageData[sectionKey].mobile_images)) ? pageData[sectionKey].mobile_images : [];
      var mobilePath = path.replace('.images', '.mobile_images');

      html += '<div class="zz-statement-images" data-path="' + path + '">';
      html += '<button type="button" class="zz-statement-images-btn">Gerer les images par position</button>';
      // Hidden data stores (desktop + mobile)
      html += '<input type="hidden" class="zz-input zz-statement-images-data" data-path="' + path + '" value="' + escAttr(JSON.stringify(images)) + '">';
      html += '<input type="hidden" class="zz-input zz-statement-mobile-data" data-path="' + mobilePath + '" value="' + escAttr(JSON.stringify(mobileImages)) + '">';
      // Thumbnails preview row
      html += '<div class="zz-statement-thumbs">';
      var posLabels = ['Haut-gauche', 'Haut-droite', 'Bas-gauche', 'Bas-droite', 'Milieu-droite'];
      for (var si = 0; si < 5; si++) {
        var img = images[si] || {};
        var thumbSrc = img.src ? resolveImageUrl(img.src) : '';
        html += '<div class="zz-statement-thumb">';
        if (thumbSrc) {
          html += '<img src="' + escAttr(thumbSrc) + '" alt="">';
        } else {
          html += '<div class="zz-statement-thumb-empty">' + (si + 1) + '</div>';
        }
        html += '<span>' + posLabels[si] + '</span>';
        html += '</div>';
      }
      html += '</div>';
      html += '</div>';
    }

    else if (field.type === 'hero-slides') {
      var slides = Array.isArray(value) ? value : [];
      html += '<div class="zz-hero-slides-field" data-path="' + path + '">';
      html += '<button type="button" class="zz-hero-slides-btn">Gerer les slides (' + slides.length + ' slides)</button>';
      html += '<input type="hidden" class="zz-input zz-hero-slides-data" data-path="' + path + '" value="' + escAttr(JSON.stringify(slides)) + '">';
      html += '<div class="zz-hero-slides-thumbs">';
      for (var hi = 0; hi < slides.length; hi++) {
        var slide = slides[hi];
        var leftSrc = slide.left ? resolveImageUrl(slide.left) : '';
        var rightSrc = slide.right ? resolveImageUrl(slide.right) : '';
        html += '<div class="zz-hero-slide-pair">';
        html += '<span class="zz-hero-slide-num">' + (hi + 1) + '</span>';
        html += '<div class="zz-hero-slide-thumb">';
        if (leftSrc) html += '<img src="' + escAttr(leftSrc) + '" alt="">';
        else html += '<div class="zz-hero-slide-thumb-empty">G</div>';
        html += '</div>';
        html += '<div class="zz-hero-slide-thumb">';
        if (rightSrc) html += '<img src="' + escAttr(rightSrc) + '" alt="">';
        else html += '<div class="zz-hero-slide-thumb-empty">D</div>';
        html += '</div>';
        html += '</div>';
      }
      html += '</div>';
      html += '</div>';
    }

    else if (field.type === 'gallery') {
      var galleryImages = Array.isArray(value) ? value : [];
      html += '<div class="zz-gallery-field" data-path="' + path + '">';
      html += '<button type="button" class="zz-gallery-btn">Gerer la galerie (' + galleryImages.length + ' photos)</button>';
      html += '<input type="hidden" class="zz-input zz-gallery-data" data-path="' + path + '" value="' + escAttr(JSON.stringify(galleryImages)) + '">';
      // Thumbnail preview
      html += '<div class="zz-gallery-thumbs">';
      var maxThumbs = Math.min(galleryImages.length, 8);
      for (var gi = 0; gi < maxThumbs; gi++) {
        var gImg = galleryImages[gi] || {};
        var gSrc = gImg.src ? resolveImageUrl(gImg.src) : '';
        html += '<div class="zz-gallery-thumb">';
        if (gSrc) html += '<img src="' + escAttr(gSrc) + '" alt="">';
        html += '</div>';
      }
      if (galleryImages.length > 8) {
        html += '<div class="zz-gallery-thumb zz-gallery-thumb-more">+' + (galleryImages.length - 8) + '</div>';
      }
      html += '</div>';
      html += '</div>';
    }

    html += '</div>';
    return html;
  }

  function buildRepeaterItem(fields, itemData, sectionKey, index) {
    var html = '<div class="zz-repeater-item" data-index="' + index + '">';
    html += '<button type="button" class="zz-repeater-remove" title="Supprimer">&times;</button>';
    for (var f = 0; f < fields.length; f++) {
      var field = fields[f];
      var value = itemData ? itemData[field.key] : '';
      var path = sectionKey + '.' + index + '.' + field.key;
      html += buildField(field, value, path);
    }
    html += '</div>';
    return html;
  }


  /* --------------------------------------------------------
     IMAGE URL HELPER
     -------------------------------------------------------- */

  function resolveImageUrl(src) {
    if (!src) return '';
    if (src.indexOf('http') === 0 || src.indexOf('//') === 0) return src;
    if (src.indexOf('wp-content/uploads/') !== -1) return zzEditor.siteUrl + src;
    return zzEditor.themeUrl + '/assets/img/' + src;
  }

  /**
   * Convertir une URL absolue en chemin relatif portable
   * ex: https://zougzoug.lan/wp-content/uploads/... → wp-content/uploads/...
   * ex: https://zougzoug.lan/.../assets/img/photo.webp → photo.webp
   */
  function toRelativePath(url) {
    var themeBase = zzEditor.themeUrl + '/assets/img/';
    if (url.indexOf(themeBase) === 0) return url.replace(themeBase, '');
    var siteBase = zzEditor.siteUrl;
    if (url.indexOf(siteBase) === 0) return url.replace(siteBase, '');
    return url;
  }


  /* --------------------------------------------------------
     ACCORDIONS
     -------------------------------------------------------- */

  function bindAccordions() {
    var headers = formEl.querySelectorAll('.zz-section-header');
    for (var i = 0; i < headers.length; i++) {
      headers[i].addEventListener('click', function () {
        this.parentElement.classList.toggle('is-open');
      });
    }
  }


  /* --------------------------------------------------------
     SAVE
     -------------------------------------------------------- */

  function bindSaveButton() {
    var btn = document.getElementById('zz-save-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      saveData();
    });
  }

  function collectData() {
    syncRichTextEditors();
    var schema = SCHEMAS[pageSlug];
    var data = JSON.parse(JSON.stringify(pageData));

    for (var s = 0; s < schema.length; s++) {
      var section = schema[s];

      if (section.repeater) {
        var repeaterEl = formEl.querySelector('.zz-repeater[data-path="' + section.key + '"]');
        if (!repeaterEl) continue;
        var items = repeaterEl.querySelectorAll('.zz-repeater-item');
        var arr = [];
        for (var r = 0; r < items.length; r++) {
          var item = {};
          // Preserve existing data that isn't in the form
          if (Array.isArray(data[section.key]) && data[section.key][r]) {
            item = JSON.parse(JSON.stringify(data[section.key][r]));
          }
          var inputs = items[r].querySelectorAll('.zz-input');
          var arrayFields = {};
          for (var ri = 0; ri < inputs.length; ri++) {
            var pathParts = inputs[ri].getAttribute('data-path').split('.');
            var fieldKey = pathParts[pathParts.length - 1];
            if (inputs[ri].classList.contains('zz-input-array')) {
              if (!arrayFields[fieldKey]) arrayFields[fieldKey] = [];
              arrayFields[fieldKey].push(inputs[ri].value);
            } else {
              item[fieldKey] = getInputValue(inputs[ri]);
            }
          }
          for (var ak in arrayFields) {
            item[ak] = arrayFields[ak];
          }
          arr.push(item);
        }
        data[section.key] = arr;

        // Collect root fields in repeater sections
        for (var rf = 0; rf < section.fields.length; rf++) {
          var rootField = section.fields[rf];
          if (!rootField.root) continue;
          var rootInput = formEl.querySelector('.zz-input[data-path="' + rootField.key + '"]');
          if (rootInput) data[rootField.key] = getInputValue(rootInput);
        }
        continue;
      }

      // Regular fields
      for (var f = 0; f < section.fields.length; f++) {
        var field = section.fields[f];
        var path = section.flat ? field.key : section.key + '.' + field.key;

        if (field.type === 'hero-slides') {
          var heroInput = formEl.querySelector('.zz-hero-slides-data[data-path="' + path + '"]');
          if (heroInput) {
            try { setNestedValue(data, path, JSON.parse(heroInput.value)); } catch (ex) { /* keep existing */ }
          }
        } else if (field.type === 'statement-images') {
          var stmtInput = formEl.querySelector('.zz-statement-images-data[data-path="' + path + '"]');
          if (stmtInput) {
            try { setNestedValue(data, path, JSON.parse(stmtInput.value)); } catch (ex) { /* keep existing */ }
          }
          // Also collect mobile images
          var mobilePath = path.replace('.images', '.mobile_images');
          var mobileInput = formEl.querySelector('.zz-statement-mobile-data[data-path="' + mobilePath + '"]');
          if (mobileInput) {
            try { setNestedValue(data, mobilePath, JSON.parse(mobileInput.value)); } catch (ex) { /* keep existing */ }
          }
        } else if (field.type === 'gallery') {
          var galleryInput = formEl.querySelector('.zz-gallery-data[data-path="' + path + '"]');
          if (galleryInput) {
            try { setNestedValue(data, path, JSON.parse(galleryInput.value)); } catch (ex) { /* keep existing */ }
          }
        } else if (field.type === 'textarea-array') {
          var textareas = formEl.querySelectorAll('.zz-input-array[data-path="' + path + '"]');
          var values = [];
          for (var t = 0; t < textareas.length; t++) {
            values.push(textareas[t].value);
          }
          setNestedValue(data, path, values);
        } else {
          var input = formEl.querySelector('.zz-input[data-path="' + path + '"]');
          if (input) {
            setNestedValue(data, path, getInputValue(input));
          }
        }
      }
    }

    return data;
  }

  function getInputValue(input) {
    if (input.type === 'number') {
      var num = parseFloat(input.value);
      return isNaN(num) ? null : num;
    }
    return input.value;
  }

  function setNestedValue(obj, path, value) {
    var parts = path.split('.');
    var current = obj;
    for (var i = 0; i < parts.length - 1; i++) {
      if (current[parts[i]] === undefined || current[parts[i]] === null) {
        current[parts[i]] = {};
      }
      current = current[parts[i]];
    }
    current[parts[parts.length - 1]] = value;
  }

  function saveData() {
    var btn = document.getElementById('zz-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<div class="zz-editor-spinner"></div> Sauvegarde...';
    setStatus('saving', 'Sauvegarde en cours...');

    var data = collectData();

    fetch(zzEditor.restUrl + pageSlug, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': zzEditor.nonce
      },
      body: JSON.stringify({ data: data })
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      if (json.success) {
        pageData = data;
        setStatus('success', 'Sauvegarde !');
        // Refresh iframe (preserve zz_preview param)
        if (iframe) {
          var frontUrl = wrap.getAttribute('data-front-url');
          var sep = frontUrl.indexOf('?') !== -1 ? '&' : '?';
          iframe.src = frontUrl + sep + 'zz_preview=1&t=' + Date.now();
        }
      } else {
        setStatus('error', json.message || 'Erreur');
      }
    })
    .catch(function () {
      setStatus('error', 'Erreur reseau');
    })
    .finally(function () {
      btn.disabled = false;
      btn.innerHTML = 'Sauvegarder';
    });
  }

  function setStatus(type, message) {
    statusEl.className = 'zz-editor-status is-' + type;
    if (type === 'success') {
      statusEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>' + escHtml(message);
    } else if (type === 'error') {
      statusEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' + escHtml(message);
    } else {
      statusEl.innerHTML = '<div class="zz-editor-spinner"></div>' + escHtml(message);
    }

    if (type === 'success') {
      setTimeout(function () {
        statusEl.className = 'zz-editor-status';
        statusEl.innerHTML = '';
      }, 3000);
    }
  }


  /* --------------------------------------------------------
     IMAGE PICKER (wp.media)
     -------------------------------------------------------- */

  function bindImagePickers() {
    formEl.addEventListener('click', function (e) {
      var btn = e.target.closest('.zz-img-btn') || e.target.closest('.zz-img-preview');
      if (!btn) return;
      e.preventDefault();

      var path = btn.getAttribute('data-path');
      openMediaPicker(path);
    });
  }

  function openMediaPicker(path) {
    if (typeof wp === 'undefined' || !wp.media) return;

    var frame = wp.media({
      title: 'Choisir une image',
      button: { text: 'Utiliser cette image' },
      multiple: false,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      var url = attachment.url;
      var filename = toRelativePath(url);

      // Update hidden input
      var input = formEl.querySelector('input.zz-input[data-path="' + path + '"]');
      if (input) input.value = filename;

      // Update preview
      var preview = formEl.querySelector('.zz-img-preview[data-path="' + path + '"]');
      if (preview) {
        preview.innerHTML = '<img src="' + escAttr(url) + '" alt="">';
      }

      // Update button text
      var imgBtn = formEl.querySelector('.zz-img-btn[data-path="' + path + '"]');
      if (imgBtn) imgBtn.textContent = 'Changer';
    });

    frame.open();
  }


  /* --------------------------------------------------------
     REPEATERS
     -------------------------------------------------------- */

  function bindRepeaters() {
    // Add item
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-repeater-add')) return;
      e.preventDefault();

      var sectionKey = e.target.getAttribute('data-path');
      var repeaterEl = e.target.closest('.zz-repeater');
      var schema = findRepeaterSchema(sectionKey);
      if (!schema) return;

      var items = repeaterEl.querySelectorAll('.zz-repeater-item');
      var newIndex = items.length;

      var div = document.createElement('div');
      div.innerHTML = buildRepeaterItem(schema.fields, null, sectionKey, newIndex);
      var newItem = div.firstChild;
      repeaterEl.insertBefore(newItem, e.target);

      // Initialize richtext editors in the new item
      var richTextareas = newItem.querySelectorAll('.zz-richtext');
      for (var rt = 0; rt < richTextareas.length; rt++) {
        if (richTextareas[rt].id) initSingleRichText(richTextareas[rt].id);
      }
    });

    // Remove item
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-repeater-remove')) return;
      e.preventDefault();

      var item = e.target.closest('.zz-repeater-item');
      if (item) {
        // Destroy richtext editors in the removed item
        var richTextareas = item.querySelectorAll('.zz-richtext');
        for (var rt = 0; rt < richTextareas.length; rt++) {
          if (richTextareas[rt].id) destroyRichTextEditor(richTextareas[rt].id);
        }
        item.remove();
      }
    });
  }

  function findRepeaterSchema(sectionKey) {
    var schema = SCHEMAS[pageSlug];
    for (var s = 0; s < schema.length; s++) {
      if (schema[s].key === sectionKey && schema[s].repeater) {
        return schema[s].repeater;
      }
    }
    return null;
  }


  /* --------------------------------------------------------
     STATEMENT IMAGES MODAL
     -------------------------------------------------------- */

  var stmtModal = null;
  var stmtPath = '';
  var stmtImages = [];
  var stmtMobileImages = [];
  var stmtActiveTab = 'desktop';
  var posLabelsDesktop = ['Haut-gauche', 'Haut-droite', 'Bas-gauche', 'Bas-droite', 'Milieu-droite'];
  var posLabelsMobile = ['Haut-gauche', 'Bas-droite', 'Bas-gauche'];

  var zzLogoSvg = '<svg viewBox="0 0 50 48" width="50" height="48" fill="none" stroke="rgba(26,26,26,0.15)" stroke-width="0.5"><path d="M10 23.6C10.7 23 11.4 22.5 12.2 22L12.2 22C17.1 19.2 23 14.2 23 6.4V0H1.5V6.2H16.7V6.4C16.7 10.3 14.2 13.8 9.1 16.7L9.1 16.7C7.9 17.4 6.8 18.2 5.7 19.1C1.8 22.8 0 26.9 0 32.4V48H18.5C18.5 48 21.3 45.4 21.3 41C21.3 37 14.8 33.7 15.4 29.9C15.9 27.2 20.1 27.3 22.3 27.4V21.4C16.1 20.4 11 23.1 10.5 29C10 35.5 16 38.6 17.3 40.5C17.5 40.7 17.5 40.8 17.5 41.1C17.5 41.4 17.1 41.8 16.7 41.8H6.3V32.4C6.3 28 7.8 25.7 10 23.6Z"/><path d="M36.2 37C34.4 37 32.8 37.4 31.3 38.1V30.9C31.3 25.5 34.6 22.4 37.2 20.3C44.9 14.1 50 11.4 50 4.7C50 1.9 48.2 0 45.4 0C39.5 0 39.2 8.7 35.3 8.7C31.5 8.7 32.8 0 32.8 0C32.8 0 27.3 0 25.9 0C25.9 0 23.8 12.7 32.7 13.3C41.9 13.9 43 4.2 45.2 4.2C45.8 4.2 46.1 4.5 46.1 5.2C46.1 9.9 38.7 12.9 33.1 17.2C29 20.3 25 23.7 25 31.7V47.3C25 47.6 25 47.8 25 48H25H31.3H31.3V47.5C31.5 45.1 33.6 43.2 36.2 43.2C38.9 43.2 41 45.4 41 48H47.3C47.3 42 42.3 37 36.2 37Z"/></svg>';

  function bindStatementImages() {
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-statement-images-btn')) return;
      e.preventDefault();
      var wrapper = e.target.closest('.zz-statement-images');
      stmtPath = wrapper.querySelector('.zz-statement-images-data').getAttribute('data-path');

      var raw = wrapper.querySelector('.zz-statement-images-data').value;
      try { stmtImages = JSON.parse(raw); } catch (ex) { stmtImages = []; }
      while (stmtImages.length < 5) stmtImages.push({ src: '', alt: '' });

      var rawMobile = wrapper.querySelector('.zz-statement-mobile-data').value;
      try { stmtMobileImages = JSON.parse(rawMobile); } catch (ex) { stmtMobileImages = []; }
      // Default mobile: use desktop images 0 and 1 if empty
      if (stmtMobileImages.length === 0) {
        stmtMobileImages = [
          { src: stmtImages[0].src || '', alt: stmtImages[0].alt || '' },
          { src: stmtImages[1].src || '', alt: stmtImages[1].alt || '' },
          { src: '', alt: '' }
        ];
      }
      while (stmtMobileImages.length < 3) stmtMobileImages.push({ src: '', alt: '' });

      stmtActiveTab = 'desktop';
      openStatementModal();
    });
  }

  function buildSlotHtml(img, index, label) {
    var imgSrc = img.src ? resolveImageUrl(img.src) : '';
    var html = '<div class="zz-stmt-slot-img">';
    if (imgSrc) {
      html += '<img src="' + escAttr(imgSrc) + '" alt="">';
    } else {
      html += '<div class="zz-stmt-slot-empty">' + (index + 1) + '</div>';
    }
    html += '<div class="zz-stmt-slot-hover">Changer</div>';
    html += '</div>';
    html += '<div class="zz-stmt-slot-label">' + label + '</div>';
    html += '<input type="text" class="zz-stmt-alt" data-slot="' + index + '" value="' + escAttr(img.alt || '') + '" placeholder="Texte alternatif">';
    return html;
  }

  function openStatementModal() {
    if (stmtModal) stmtModal.remove();

    var overlay = document.createElement('div');
    overlay.className = 'zz-stmt-modal-overlay';

    var html = '<div class="zz-stmt-modal">';

    // Header with tabs
    html += '<div class="zz-stmt-modal-header">';
    html += '<h3>Images du Statement</h3>';
    html += '<div class="zz-stmt-tabs">';
    html += '<button type="button" class="zz-stmt-tab is-active" data-tab="desktop">';
    html += '<svg width="16" height="14" viewBox="0 0 24 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="1" width="20" height="14" rx="2"/><line x1="8" y1="19" x2="16" y2="19"/><line x1="12" y1="15" x2="12" y2="19"/></svg>';
    html += ' Desktop</button>';
    html += '<button type="button" class="zz-stmt-tab" data-tab="mobile">';
    html += '<svg width="12" height="16" viewBox="0 0 16 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="1" width="12" height="22" rx="2"/><line x1="6" y1="20" x2="10" y2="20"/></svg>';
    html += ' Mobile</button>';
    html += '</div>';
    html += '<button type="button" class="zz-stmt-modal-close">&times;</button>';
    html += '</div>';

    // Desktop view
    html += '<div class="zz-stmt-modal-body">';
    html += '<div class="zz-stmt-view zz-stmt-view--desktop is-active">';
    html += '<div class="zz-stmt-layout">';
    html += '<div class="zz-stmt-center">' + zzLogoSvg + '<span>Texte central</span></div>';
    for (var i = 0; i < 5; i++) {
      var img = stmtImages[i] || { src: '', alt: '' };
      html += '<div class="zz-stmt-slot zz-stmt-slot--' + (i + 1) + '" data-slot="' + i + '" data-view="desktop">';
      html += buildSlotHtml(img, i, posLabelsDesktop[i]);
      html += '</div>';
    }
    html += '</div>'; // .zz-stmt-layout
    html += '</div>'; // .zz-stmt-view--desktop

    // Mobile view
    html += '<div class="zz-stmt-view zz-stmt-view--mobile">';
    html += '<div class="zz-stmt-phone">';
    html += '<div class="zz-stmt-phone-notch"></div>';
    html += '<div class="zz-stmt-phone-screen">';
    html += '<div class="zz-stmt-center">' + zzLogoSvg + '<span>Texte</span></div>';
    for (var m = 0; m < 3; m++) {
      var mImg = stmtMobileImages[m] || { src: '', alt: '' };
      html += '<div class="zz-stmt-slot zz-stmt-mslot--' + (m + 1) + '" data-slot="' + m + '" data-view="mobile">';
      html += buildSlotHtml(mImg, m, posLabelsMobile[m]);
      html += '</div>';
    }
    html += '</div>'; // .zz-stmt-phone-screen
    html += '</div>'; // .zz-stmt-phone
    html += '</div>'; // .zz-stmt-view--mobile

    html += '</div>'; // .zz-stmt-modal-body

    // Footer
    html += '<div class="zz-stmt-modal-footer">';
    html += '<button type="button" class="zz-stmt-modal-save">Valider</button>';
    html += '</div>';

    html += '</div>'; // .zz-stmt-modal
    overlay.innerHTML = html;
    document.body.appendChild(overlay);
    stmtModal = overlay;

    overlay.offsetHeight;
    overlay.classList.add('is-open');

    // Bind close
    overlay.querySelector('.zz-stmt-modal-close').addEventListener('click', closeStatementModal);
    overlay.querySelector('.zz-stmt-modal-save').addEventListener('click', saveStatementModal);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeStatementModal();
    });

    // Bind tabs
    var tabs = overlay.querySelectorAll('.zz-stmt-tab');
    for (var t = 0; t < tabs.length; t++) {
      tabs[t].addEventListener('click', function () {
        var tab = this.getAttribute('data-tab');
        stmtActiveTab = tab;
        for (var tt = 0; tt < tabs.length; tt++) tabs[tt].classList.remove('is-active');
        this.classList.add('is-active');
        var views = overlay.querySelectorAll('.zz-stmt-view');
        for (var v = 0; v < views.length; v++) views[v].classList.remove('is-active');
        overlay.querySelector('.zz-stmt-view--' + tab).classList.add('is-active');
      });
    }

    // Bind slot clicks
    var slots = overlay.querySelectorAll('.zz-stmt-slot-img');
    for (var s = 0; s < slots.length; s++) {
      slots[s].addEventListener('click', function () {
        var slotEl = this.parentElement;
        var slotIndex = parseInt(slotEl.getAttribute('data-slot'), 10);
        var view = slotEl.getAttribute('data-view');
        openStatementMediaPicker(slotIndex, view);
      });
    }
  }

  function openStatementMediaPicker(index, view) {
    if (typeof wp === 'undefined' || !wp.media) return;
    var labels = view === 'mobile' ? posLabelsMobile : posLabelsDesktop;
    var imagesArr = view === 'mobile' ? stmtMobileImages : stmtImages;

    var frame = wp.media({
      title: labels[index] + ' — Choisir une image',
      button: { text: 'Utiliser cette image' },
      multiple: false,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      var url = attachment.url;
      var filename = toRelativePath(url);

      imagesArr[index].src = filename;

      // Update slot preview in modal
      var selector = '.zz-stmt-slot[data-slot="' + index + '"][data-view="' + view + '"] .zz-stmt-slot-img';
      var slot = stmtModal.querySelector(selector);
      if (slot) {
        slot.innerHTML = '<img src="' + escAttr(url) + '" alt=""><div class="zz-stmt-slot-hover">Changer</div>';
      }
    });

    frame.open();
  }

  function saveStatementModal() {
    // Collect desktop alt texts
    var desktopAlts = stmtModal.querySelectorAll('.zz-stmt-view--desktop .zz-stmt-alt');
    for (var i = 0; i < desktopAlts.length; i++) {
      var idx = parseInt(desktopAlts[i].getAttribute('data-slot'), 10);
      if (stmtImages[idx]) stmtImages[idx].alt = desktopAlts[i].value;
    }

    // Collect mobile alt texts
    var mobileAlts = stmtModal.querySelectorAll('.zz-stmt-view--mobile .zz-stmt-alt');
    for (var j = 0; j < mobileAlts.length; j++) {
      var mIdx = parseInt(mobileAlts[j].getAttribute('data-slot'), 10);
      if (stmtMobileImages[mIdx]) stmtMobileImages[mIdx].alt = mobileAlts[j].value;
    }

    // Filter desktop (remove empty)
    var cleanedDesktop = [];
    for (var d = 0; d < stmtImages.length; d++) {
      if (stmtImages[d].src) cleanedDesktop.push(stmtImages[d]);
    }

    // Filter mobile (remove empty)
    var cleanedMobile = [];
    for (var mb = 0; mb < stmtMobileImages.length; mb++) {
      if (stmtMobileImages[mb].src) cleanedMobile.push(stmtMobileImages[mb]);
    }

    // Update hidden inputs
    var desktopInput = formEl.querySelector('.zz-statement-images-data[data-path="' + stmtPath + '"]');
    if (desktopInput) desktopInput.value = JSON.stringify(cleanedDesktop);

    var mobilePath = stmtPath.replace('.images', '.mobile_images');
    var mobileInput = formEl.querySelector('.zz-statement-mobile-data[data-path="' + mobilePath + '"]');
    if (mobileInput) mobileInput.value = JSON.stringify(cleanedMobile);

    updateStatementThumbs(cleanedDesktop);
    closeStatementModal();
  }

  function updateStatementThumbs(images) {
    var wrapper = formEl.querySelector('.zz-statement-images[data-path="' + stmtPath + '"]');
    if (!wrapper) return;
    var thumbs = wrapper.querySelectorAll('.zz-statement-thumb');
    for (var i = 0; i < thumbs.length; i++) {
      var img = images[i] || {};
      var thumbSrc = img.src ? resolveImageUrl(img.src) : '';
      if (thumbSrc) {
        var target = thumbs[i].querySelector('img, .zz-statement-thumb-empty');
        if (target) target.outerHTML = '<img src="' + escAttr(thumbSrc) + '" alt="">';
      }
    }
  }

  function closeStatementModal() {
    if (!stmtModal) return;
    stmtModal.classList.remove('is-open');
    setTimeout(function () {
      if (stmtModal) stmtModal.remove();
      stmtModal = null;
    }, 250);
  }


  /* --------------------------------------------------------
     HERO SLIDES MODAL
     -------------------------------------------------------- */

  var heroModal = null;
  var heroPath = '';
  var heroSlides = [];

  function bindHeroSlides() {
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-hero-slides-btn')) return;
      e.preventDefault();
      var wrapper = e.target.closest('.zz-hero-slides-field');
      heroPath = wrapper.querySelector('.zz-hero-slides-data').getAttribute('data-path');
      var raw = wrapper.querySelector('.zz-hero-slides-data').value;
      try { heroSlides = JSON.parse(raw); } catch (ex) { heroSlides = []; }
      openHeroSlidesModal();
    });
  }

  function openHeroSlidesModal() {
    if (heroModal) heroModal.remove();

    var overlay = document.createElement('div');
    overlay.className = 'zz-hero-modal-overlay';

    var html = '<div class="zz-hero-modal">';

    // Header
    html += '<div class="zz-hero-modal-header">';
    html += '<h3>Slides du Hero</h3>';
    html += '<span class="zz-hero-count">' + heroSlides.length + ' slides</span>';
    html += '<button type="button" class="zz-hero-modal-close">&times;</button>';
    html += '</div>';

    // Body
    html += '<div class="zz-hero-modal-body">';
    html += '<div class="zz-hero-rows" id="zz-hero-rows">';
    for (var i = 0; i < heroSlides.length; i++) {
      html += buildHeroSlideRow(heroSlides[i], i);
    }
    html += '</div>';
    html += '<button type="button" class="zz-hero-add-btn">+ Ajouter une slide</button>';
    html += '</div>';

    // Footer
    html += '<div class="zz-hero-modal-footer">';
    html += '<button type="button" class="zz-hero-modal-save">Valider</button>';
    html += '</div>';

    html += '</div>';
    overlay.innerHTML = html;
    document.body.appendChild(overlay);
    heroModal = overlay;

    overlay.offsetHeight;
    overlay.classList.add('is-open');

    // Bind events
    overlay.querySelector('.zz-hero-modal-close').addEventListener('click', closeHeroModal);
    overlay.querySelector('.zz-hero-modal-save').addEventListener('click', saveHeroModal);
    overlay.querySelector('.zz-hero-add-btn').addEventListener('click', addHeroSlide);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeHeroModal();
    });

    // Bind slot clicks + remove buttons
    bindHeroModalSlots();
  }

  function buildHeroSlideRow(slide, index) {
    var leftSrc = slide.left ? resolveImageUrl(slide.left) : '';
    var rightSrc = slide.right ? resolveImageUrl(slide.right) : '';

    var html = '<div class="zz-hero-row" data-index="' + index + '">';
    html += '<div class="zz-hero-row-header">';
    html += '<span class="zz-hero-row-num">Slide ' + (index + 1) + '</span>';
    html += '<button type="button" class="zz-hero-row-remove" data-index="' + index + '" title="Supprimer">&times;</button>';
    html += '</div>';
    html += '<div class="zz-hero-row-pair">';

    // Left
    html += '<div class="zz-hero-row-slot" data-side="left" data-index="' + index + '">';
    html += '<div class="zz-hero-row-img">';
    if (leftSrc) {
      html += '<img src="' + escAttr(leftSrc) + '" alt="">';
    } else {
      html += '<div class="zz-hero-row-empty"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
    }
    html += '<div class="zz-hero-row-hover">Changer</div>';
    html += '</div>';
    html += '<span class="zz-hero-slot-label">Gauche</span>';
    html += '</div>';

    // Right
    html += '<div class="zz-hero-row-slot" data-side="right" data-index="' + index + '">';
    html += '<div class="zz-hero-row-img">';
    if (rightSrc) {
      html += '<img src="' + escAttr(rightSrc) + '" alt="">';
    } else {
      html += '<div class="zz-hero-row-empty"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';
    }
    html += '<div class="zz-hero-row-hover">Changer</div>';
    html += '</div>';
    html += '<span class="zz-hero-slot-label">Droite</span>';
    html += '</div>';

    html += '</div>'; // .zz-hero-row-pair
    html += '</div>'; // .zz-hero-row
    return html;
  }

  function bindHeroModalSlots() {
    if (!heroModal) return;

    // Click on image slot to change
    heroModal.addEventListener('click', function (e) {
      var imgEl = e.target.closest('.zz-hero-row-img');
      if (!imgEl) return;
      var slot = imgEl.parentElement;
      var side = slot.getAttribute('data-side');
      var index = parseInt(slot.getAttribute('data-index'), 10);
      openHeroMediaPicker(index, side);
    });

    // Click remove
    heroModal.addEventListener('click', function (e) {
      var btn = e.target.closest('.zz-hero-row-remove');
      if (!btn) return;
      e.preventDefault();
      var idx = parseInt(btn.getAttribute('data-index'), 10);
      heroSlides.splice(idx, 1);
      refreshHeroRows();
    });
  }

  function openHeroMediaPicker(index, side) {
    if (typeof wp === 'undefined' || !wp.media) return;

    var frame = wp.media({
      title: 'Slide ' + (index + 1) + ' — Image ' + (side === 'left' ? 'gauche' : 'droite'),
      button: { text: 'Utiliser cette image' },
      multiple: false,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      var url = attachment.url;
      var filename = toRelativePath(url);

      heroSlides[index][side] = filename;

      // Update slot preview in modal
      var slot = heroModal.querySelector('.zz-hero-row-slot[data-side="' + side + '"][data-index="' + index + '"] .zz-hero-row-img');
      if (slot) {
        slot.innerHTML = '<img src="' + escAttr(url) + '" alt=""><div class="zz-hero-row-hover">Changer</div>';
      }
    });

    frame.open();
  }

  function addHeroSlide() {
    heroSlides.push({ left: '', right: '' });
    refreshHeroRows();
  }

  function refreshHeroRows() {
    var container = heroModal.querySelector('#zz-hero-rows');
    var html = '';
    for (var i = 0; i < heroSlides.length; i++) {
      html += buildHeroSlideRow(heroSlides[i], i);
    }
    container.innerHTML = html;

    var countEl = heroModal.querySelector('.zz-hero-count');
    if (countEl) countEl.textContent = heroSlides.length + ' slides';
  }

  function saveHeroModal() {
    // Update hidden input
    var input = formEl.querySelector('.zz-hero-slides-data[data-path="' + heroPath + '"]');
    if (input) input.value = JSON.stringify(heroSlides);

    // Update thumbnails in field
    updateHeroSlidesThumbs();

    // Update button text
    var btn = formEl.querySelector('.zz-hero-slides-field[data-path="' + heroPath + '"] .zz-hero-slides-btn');
    if (btn) btn.textContent = 'Gerer les slides (' + heroSlides.length + ' slides)';

    closeHeroModal();
  }

  function updateHeroSlidesThumbs() {
    var wrapper = formEl.querySelector('.zz-hero-slides-field[data-path="' + heroPath + '"]');
    if (!wrapper) return;
    var thumbs = wrapper.querySelector('.zz-hero-slides-thumbs');
    if (!thumbs) return;
    var html = '';
    for (var i = 0; i < heroSlides.length; i++) {
      var slide = heroSlides[i];
      var leftSrc = slide.left ? resolveImageUrl(slide.left) : '';
      var rightSrc = slide.right ? resolveImageUrl(slide.right) : '';
      html += '<div class="zz-hero-slide-pair">';
      html += '<span class="zz-hero-slide-num">' + (i + 1) + '</span>';
      html += '<div class="zz-hero-slide-thumb">';
      if (leftSrc) html += '<img src="' + escAttr(leftSrc) + '" alt="">';
      else html += '<div class="zz-hero-slide-thumb-empty">G</div>';
      html += '</div>';
      html += '<div class="zz-hero-slide-thumb">';
      if (rightSrc) html += '<img src="' + escAttr(rightSrc) + '" alt="">';
      else html += '<div class="zz-hero-slide-thumb-empty">D</div>';
      html += '</div>';
      html += '</div>';
    }
    thumbs.innerHTML = html;
  }

  function closeHeroModal() {
    if (!heroModal) return;
    heroModal.classList.remove('is-open');
    setTimeout(function () {
      if (heroModal) heroModal.remove();
      heroModal = null;
    }, 250);
  }


  /* --------------------------------------------------------
     GALLERY MODAL (drag-and-drop reorder, add, remove)
     -------------------------------------------------------- */

  var galleryModal = null;
  var galleryPath = '';
  var galleryImages = [];
  var galleryDragIndex = null;

  function bindGallery() {
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-gallery-btn')) return;
      e.preventDefault();
      var wrapper = e.target.closest('.zz-gallery-field');
      galleryPath = wrapper.querySelector('.zz-gallery-data').getAttribute('data-path');
      var raw = wrapper.querySelector('.zz-gallery-data').value;
      try { galleryImages = JSON.parse(raw); } catch (ex) { galleryImages = []; }
      openGalleryModal();
    });
  }


  /* --------------------------------------------------------
     SEO COUNTERS — live character count
     -------------------------------------------------------- */

  function bindSeoCounters() {
    var inputs = formEl.querySelectorAll('.zz-seo-input');
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].addEventListener('input', function () {
        var path = this.getAttribute('data-path');
        var counter = formEl.querySelector('.zz-counter[data-path="' + path + '"]');
        if (!counter) return;
        var max = parseInt(counter.getAttribute('data-max'), 10);
        var len = this.value.length;
        counter.textContent = len + '/' + max;
        if (len > max) {
          counter.classList.add('zz-counter--over');
        } else {
          counter.classList.remove('zz-counter--over');
        }
      });
    }
  }

  function openGalleryModal() {
    if (galleryModal) galleryModal.remove();

    var overlay = document.createElement('div');
    overlay.className = 'zz-gal-modal-overlay';

    var html = '<div class="zz-gal-modal">';

    // Header
    html += '<div class="zz-gal-modal-header">';
    html += '<h3>Galerie photos</h3>';
    html += '<span class="zz-gal-count">' + galleryImages.length + ' photos</span>';
    html += '<button type="button" class="zz-gal-add-btn">+ Ajouter</button>';
    html += '<button type="button" class="zz-gal-modal-close">&times;</button>';
    html += '</div>';

    // Grid
    html += '<div class="zz-gal-modal-body">';
    html += '<div class="zz-gal-grid" id="zz-gal-grid">';
    html += buildGalleryGrid();
    html += '</div>';
    html += '</div>';

    // Footer
    html += '<div class="zz-gal-modal-footer">';
    html += '<button type="button" class="zz-gal-modal-save">Valider</button>';
    html += '</div>';

    html += '</div>';
    overlay.innerHTML = html;
    document.body.appendChild(overlay);
    galleryModal = overlay;

    overlay.offsetHeight;
    overlay.classList.add('is-open');

    // Bind events
    overlay.querySelector('.zz-gal-modal-close').addEventListener('click', closeGalleryModal);
    overlay.querySelector('.zz-gal-modal-save').addEventListener('click', saveGalleryModal);
    overlay.querySelector('.zz-gal-add-btn').addEventListener('click', addGalleryImages);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeGalleryModal();
    });

    bindGalleryGrid();
  }

  function buildGalleryGrid() {
    var html = '';
    for (var i = 0; i < galleryImages.length; i++) {
      var img = galleryImages[i];
      var src = img.src ? resolveImageUrl(img.src) : '';
      html += '<div class="zz-gal-card" draggable="true" data-index="' + i + '">';
      html += '<div class="zz-gal-card-img">';
      if (src) html += '<img src="' + escAttr(src) + '" alt="" loading="lazy">';
      html += '</div>';
      html += '<input type="text" class="zz-gal-alt" data-index="' + i + '" value="' + escAttr(img.alt || '') + '" placeholder="Alt">';
      html += '<button type="button" class="zz-gal-remove" data-index="' + i + '" title="Supprimer">&times;</button>';
      html += '</div>';
    }
    return html;
  }

  function reindexGalleryCards() {
    var grid = galleryModal.querySelector('#zz-gal-grid');
    var cards = grid.querySelectorAll('.zz-gal-card');
    for (var i = 0; i < cards.length; i++) {
      cards[i].setAttribute('data-index', i);
      var alt = cards[i].querySelector('.zz-gal-alt');
      if (alt) alt.setAttribute('data-index', i);
      var rm = cards[i].querySelector('.zz-gal-remove');
      if (rm) rm.setAttribute('data-index', i);
    }
  }

  function bindGalleryGrid() {
    var grid = galleryModal.querySelector('#zz-gal-grid');
    var dragCard = null;

    // Drag and drop — move DOM nodes instead of rebuilding
    grid.addEventListener('dragstart', function (e) {
      var card = e.target.closest('.zz-gal-card');
      if (!card) return;
      dragCard = card;
      galleryDragIndex = parseInt(card.getAttribute('data-index'), 10);
      requestAnimationFrame(function () { card.classList.add('is-dragging'); });
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', '');
    });

    grid.addEventListener('dragend', function () {
      if (dragCard) dragCard.classList.remove('is-dragging');
      dragCard = null;
      galleryDragIndex = null;
      var overs = grid.querySelectorAll('.is-drag-over');
      for (var o = 0; o < overs.length; o++) overs[o].classList.remove('is-drag-over');
    });

    grid.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      var card = e.target.closest('.zz-gal-card');
      if (!card || card === dragCard) return;
      var overs = grid.querySelectorAll('.is-drag-over');
      for (var o = 0; o < overs.length; o++) overs[o].classList.remove('is-drag-over');
      card.classList.add('is-drag-over');
    });

    grid.addEventListener('drop', function (e) {
      e.preventDefault();
      var targetCard = e.target.closest('.zz-gal-card');
      if (!targetCard || !dragCard || targetCard === dragCard) return;

      collectGalleryAlts();

      var dropIndex = parseInt(targetCard.getAttribute('data-index'), 10);

      // Move DOM node directly (no innerHTML rebuild)
      if (galleryDragIndex < dropIndex) {
        grid.insertBefore(dragCard, targetCard.nextSibling);
      } else {
        grid.insertBefore(dragCard, targetCard);
      }

      // Sync data array
      var moved = galleryImages.splice(galleryDragIndex, 1)[0];
      galleryImages.splice(dropIndex, 0, moved);

      reindexGalleryCards();
      updateGalleryCount();
    });

    // Remove — remove single DOM node instead of rebuilding
    grid.addEventListener('click', function (e) {
      var btn = e.target.closest('.zz-gal-remove');
      if (!btn) return;
      e.preventDefault();
      var card = btn.closest('.zz-gal-card');
      var idx = parseInt(btn.getAttribute('data-index'), 10);
      collectGalleryAlts();
      galleryImages.splice(idx, 1);
      card.remove();
      reindexGalleryCards();
      updateGalleryCount();
    });
  }

  function collectGalleryAlts() {
    if (!galleryModal) return;
    var alts = galleryModal.querySelectorAll('.zz-gal-alt');
    for (var i = 0; i < alts.length; i++) {
      var idx = parseInt(alts[i].getAttribute('data-index'), 10);
      if (galleryImages[idx]) galleryImages[idx].alt = alts[i].value;
    }
  }

  function updateGalleryCount() {
    if (!galleryModal) return;
    var countEl = galleryModal.querySelector('.zz-gal-count');
    if (countEl) countEl.textContent = galleryImages.length + ' photos';
  }

  function addGalleryImages() {
    if (typeof wp === 'undefined' || !wp.media) return;

    var frame = wp.media({
      title: 'Ajouter des photos',
      button: { text: 'Ajouter a la galerie' },
      multiple: true,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      collectGalleryAlts();
      var selection = frame.state().get('selection');
      selection.each(function (attachment) {
        var att = attachment.toJSON();
        galleryImages.push({
          src: toRelativePath(att.url),
          alt: att.alt || att.title || ''
        });
      });
      var grid = galleryModal.querySelector('#zz-gal-grid');
      grid.innerHTML = buildGalleryGrid();
      updateGalleryCount();
    });

    frame.open();
  }

  function saveGalleryModal() {
    collectGalleryAlts();

    // Update hidden input
    var input = formEl.querySelector('.zz-gallery-data[data-path="' + galleryPath + '"]');
    if (input) input.value = JSON.stringify(galleryImages);

    // Update thumbnails in field
    updateGalleryThumbs();
    // Update button text
    var btn = formEl.querySelector('.zz-gallery-field[data-path="' + galleryPath + '"] .zz-gallery-btn');
    if (btn) btn.textContent = 'Gerer la galerie (' + galleryImages.length + ' photos)';

    closeGalleryModal();
  }

  function updateGalleryThumbs() {
    var wrapper = formEl.querySelector('.zz-gallery-field[data-path="' + galleryPath + '"]');
    if (!wrapper) return;
    var thumbs = wrapper.querySelector('.zz-gallery-thumbs');
    if (!thumbs) return;
    var html = '';
    var maxThumbs = Math.min(galleryImages.length, 8);
    for (var i = 0; i < maxThumbs; i++) {
      var src = galleryImages[i].src ? resolveImageUrl(galleryImages[i].src) : '';
      html += '<div class="zz-gallery-thumb">';
      if (src) html += '<img src="' + escAttr(src) + '" alt="">';
      html += '</div>';
    }
    if (galleryImages.length > 8) {
      html += '<div class="zz-gallery-thumb zz-gallery-thumb-more">+' + (galleryImages.length - 8) + '</div>';
    }
    thumbs.innerHTML = html;
  }

  function closeGalleryModal() {
    if (!galleryModal) return;
    galleryModal.classList.remove('is-open');
    setTimeout(function () {
      if (galleryModal) galleryModal.remove();
      galleryModal = null;
    }, 250);
  }


  /* --------------------------------------------------------
     RICHTEXT (TinyMCE) — WYSIWYG editor for HTML content
     -------------------------------------------------------- */

  var richTextIds = [];

  function initRichTextEditors() {
    if (typeof wp === 'undefined' || !wp.editor) return;

    var textareas = formEl.querySelectorAll('.zz-richtext');
    for (var i = 0; i < textareas.length; i++) {
      var id = textareas[i].id;
      if (!id) continue;
      initSingleRichText(id);
    }
  }

  function initSingleRichText(id) {
    if (typeof wp === 'undefined' || !wp.editor) return;
    if (richTextIds.indexOf(id) !== -1) return;

    wp.editor.initialize(id, {
      tinymce: {
        wpautop: false,
        toolbar1: 'bold,italic,link,unlink,bullist,numlist,blockquote,separator,undo,redo,removeformat',
        toolbar2: '',
        plugins: 'lists,link,paste,wordpress,wplink',
        block_formats: 'Paragraph=p',
        valid_elements: 'p,br,strong,em,a[href|target|rel],ul,li,span[class],blockquote',
        forced_root_block: 'p',
        height: 200,
        menubar: false,
        statusbar: false,
        branding: false,
        content_style: "body { font-family: 'General Sans', -apple-system, sans-serif; font-size: 14px; line-height: 1.6; color: #1A1A1A; }"
      },
      quicktags: {
        buttons: 'strong,em,link,ul,li,close'
      },
      mediaButtons: false
    });

    richTextIds.push(id);
  }

  function syncRichTextEditors() {
    if (typeof wp === 'undefined' || !wp.editor) return;
    for (var i = 0; i < richTextIds.length; i++) {
      var id = richTextIds[i];
      var textarea = document.getElementById(id);
      if (!textarea) continue;
      // Get content from TinyMCE if it's active
      if (typeof tinymce !== 'undefined') {
        var editor = tinymce.get(id);
        if (editor && !editor.isHidden()) {
          textarea.value = editor.getContent();
        }
      }
    }
  }

  function destroyRichTextEditor(id) {
    if (typeof wp === 'undefined' || !wp.editor) return;
    try { wp.editor.remove(id); } catch (ex) { /* ignore */ }
    var idx = richTextIds.indexOf(id);
    if (idx !== -1) richTextIds.splice(idx, 1);
  }

  function destroyAllRichTextEditors() {
    var ids = richTextIds.slice();
    for (var i = 0; i < ids.length; i++) {
      destroyRichTextEditor(ids[i]);
    }
  }


  /* --------------------------------------------------------
     UTILS
     -------------------------------------------------------- */

  function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function escAttr(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;');
  }

})();
