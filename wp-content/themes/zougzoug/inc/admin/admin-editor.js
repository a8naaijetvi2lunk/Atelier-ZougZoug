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
        key: 'hero', title: 'Hero', icon: 'dashicons-format-image',
        fields: [
          { key: 'tagline', label: 'Accroche', type: 'textarea' }
        ]
      },
      {
        key: 'statement', title: 'Statement', icon: 'dashicons-editor-quote',
        fields: [
          { key: 'text', label: 'Texte', type: 'textarea' }
        ]
      },
      {
        key: 'luminaires', title: 'Luminaires', icon: 'dashicons-lightbulb',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'texts', label: 'Textes', type: 'textarea-array' },
          { key: 'ref_label', label: 'Label references', type: 'text' },
          { key: 'ref_text', label: 'Texte references', type: 'text' },
          { key: 'img_main', label: 'Image principale', type: 'image' },
          { key: 'img_main_alt', label: 'Alt image principale', type: 'text' },
          { key: 'img_context', label: 'Image contexte', type: 'image' },
          { key: 'img_context_alt', label: 'Alt image contexte', type: 'text' }
        ]
      },
      {
        key: 'vaisselle', title: 'Art de la table', icon: 'dashicons-food',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'texts', label: 'Textes', type: 'textarea-array' },
          { key: 'ref_label', label: 'Label references', type: 'text' },
          { key: 'ref_text', label: 'Texte references', type: 'text' },
          { key: 'img_main', label: 'Image principale', type: 'image' },
          { key: 'img_main_alt', label: 'Alt image principale', type: 'text' },
          { key: 'img_mood', label: 'Image ambiance', type: 'image' },
          { key: 'img_mood_alt', label: 'Alt image ambiance', type: 'text' }
        ]
      }
    ],

    about: [
      {
        key: 'hero', title: 'Hero', icon: 'dashicons-admin-users',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'name', label: 'Nom', type: 'text' },
          { key: 'text', label: 'Texte biographie', type: 'textarea' },
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
          { key: 'text', label: 'Texte', type: 'textarea' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      }
    ],

    contact: [
      {
        key: 'form', title: 'Formulaire', icon: 'dashicons-email',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'title', label: 'Titre', type: 'textarea' }
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
          { key: 'address', label: 'Adresse', type: 'textarea' },
          { key: 'instagram_handle', label: 'Instagram', type: 'text' },
          { key: 'photo', label: 'Photo', type: 'image' },
          { key: 'photo_alt', label: 'Alt photo', type: 'text' }
        ]
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'textarea' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
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
          { key: 'text', label: 'Texte', type: 'textarea' }
        ]
      },
      {
        key: 'offres', title: 'Offres', icon: 'dashicons-tickets-alt',
        fields: [],
        repeater: {
          fields: [
            { key: 'nom', label: 'Nom', type: 'text' },
            { key: 'description', label: 'Description', type: 'textarea' },
            { key: 'prix', label: 'Prix (€)', type: 'number' }
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
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'textarea' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
        ]
      }
    ],

    revendeurs: [
      {
        key: 'hero', title: 'Hero', icon: 'dashicons-format-image',
        fields: [
          { key: 'label', label: 'Label', type: 'text' },
          { key: 'title', label: 'Titre', type: 'textarea' },
          { key: 'text', label: 'Sous-titre', type: 'text' }
        ]
      },
      {
        key: 'lieux', title: 'Points de vente', icon: 'dashicons-store',
        fields: [],
        repeater: {
          fields: [
            { key: 'type', label: 'Type', type: 'text' },
            { key: 'nom', label: 'Nom', type: 'text' },
            { key: 'adresse', label: 'Adresse', type: 'textarea' },
            { key: 'note', label: 'Note', type: 'text' },
            { key: 'link', label: 'Lien', type: 'url' },
            { key: 'link_text', label: 'Texte du lien', type: 'text' }
          ]
        }
      },
      {
        key: 'cta', title: 'CTA final', icon: 'dashicons-megaphone',
        fields: [
          { key: 'text', label: 'Texte', type: 'textarea' },
          { key: 'button', label: 'Bouton', type: 'text' },
          { key: 'url', label: 'URL', type: 'url' }
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
          { key: 'address', label: 'Adresse', type: 'textarea' },
          { key: 'instagram', label: 'URL Instagram', type: 'url' }
        ]
      },
      {
        key: 'meta', title: 'Meta / SEO', icon: 'dashicons-search',
        fields: [
          { key: 'description', label: 'Meta description', type: 'textarea' },
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

    loadData();
  });


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
        var value = sectionData[field.key];
        var path = section.flat ? field.key : section.key + '.' + field.key;
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
    return zzEditor.themeUrl + '/assets/img/' + src;
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
          for (var ri = 0; ri < inputs.length; ri++) {
            var pathParts = inputs[ri].getAttribute('data-path').split('.');
            var fieldKey = pathParts[pathParts.length - 1];
            item[fieldKey] = getInputValue(inputs[ri]);
          }
          arr.push(item);
        }
        data[section.key] = arr;
        continue;
      }

      // Regular fields
      for (var f = 0; f < section.fields.length; f++) {
        var field = section.fields[f];
        var path = section.flat ? field.key : section.key + '.' + field.key;

        if (field.type === 'textarea-array') {
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
        // Refresh iframe
        if (iframe) {
          iframe.src = iframe.src;
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
      // Try to use a relative path if it's in the theme
      var themeBase = zzEditor.themeUrl + '/assets/img/';
      var filename = url;
      if (url.indexOf(themeBase) === 0) {
        filename = url.replace(themeBase, '');
      }

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
      repeaterEl.insertBefore(div.firstChild, e.target);
    });

    // Remove item
    formEl.addEventListener('click', function (e) {
      if (!e.target.classList.contains('zz-repeater-remove')) return;
      e.preventDefault();

      var item = e.target.closest('.zz-repeater-item');
      if (item) item.remove();
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
