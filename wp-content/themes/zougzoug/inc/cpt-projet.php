<?php
/**
 * Custom Post Type — Projet + Taxonomy + Meta
 */

add_action('init', function () {
	// CPT Projet
	register_post_type('projet', [
		'labels' => [
			'name'               => 'Projets',
			'singular_name'      => 'Projet',
			'add_new'            => 'Ajouter un projet',
			'add_new_item'       => 'Ajouter un nouveau projet',
			'edit_item'          => 'Modifier le projet',
			'view_item'          => 'Voir le projet',
			'all_items'          => 'Tous les projets',
			'search_items'       => 'Rechercher un projet',
			'not_found'          => 'Aucun projet trouvé',
			'not_found_in_trash' => 'Aucun projet dans la corbeille',
		],
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => ['title', 'thumbnail', 'page-attributes'],
		'rewrite'      => ['slug' => 'collaborations'],
		'show_in_rest' => true,
	]);

	// Taxonomy categorie_projet
	register_taxonomy('categorie_projet', 'projet', [
		'labels' => [
			'name'          => 'Catégories de projet',
			'singular_name' => 'Catégorie de projet',
			'add_new_item'  => 'Ajouter une catégorie',
			'edit_item'     => 'Modifier la catégorie',
			'all_items'     => 'Toutes les catégories',
			'search_items'  => 'Rechercher une catégorie',
		],
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => ['slug' => 'categorie-projet'],
		'show_in_rest' => true,
	]);

	// Meta fields
	$meta_fields = [
		'_projet_client'       => 'string',
		'_projet_year'         => 'string',
		'_projet_location'     => 'string',
		'_projet_description'  => 'string',
		'_projet_short_desc'   => 'string',
		'_projet_nature'       => 'string',
		'_projet_materiaux'    => 'string',
		'_projet_instagram'    => 'string',
		'_projet_website'      => 'string',
		'_projet_collaborateur' => 'string',
	];

	foreach ($meta_fields as $key => $type) {
		register_post_meta('projet', $key, [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => $type,
		]);
	}

	register_post_meta('projet', '_projet_gallery', [
		'show_in_rest' => [
			'schema' => [
				'type'  => 'array',
				'items' => ['type' => 'integer'],
			],
		],
		'single' => true,
		'type'   => 'array',
	]);
});

/**
 * Rediriger le permalink des projets vers la page Collaborations
 * Le CPT n'a pas de template single — tout est affiché via la lightbox JS
 */
add_filter('post_type_link', function ($link, $post) {
	if ($post->post_type !== 'projet') return $link;
	$page = get_page_by_path('collaborations');
	if (!$page) return $link;
	return get_permalink($page) . '#projet-' . $post->post_name;
}, 10, 2);

// Créer les termes par défaut
add_action('init', function () {
	$terms = [
		'art-de-la-table' => 'Art de la table',
		'luminaires'      => 'Luminaires',
		'accessoires'     => 'Accessoires',
	];
	foreach ($terms as $slug => $name) {
		if (!term_exists($slug, 'categorie_projet')) {
			wp_insert_term($name, 'categorie_projet', ['slug' => $slug]);
		}
	}
}, 20);

/**
 * Metaboxes custom pour les champs projet
 */
add_action('add_meta_boxes', function () {
	add_meta_box(
		'zz_projet_details',
		'Détails du projet',
		'zz_projet_metabox_render',
		'projet',
		'normal',
		'high'
	);
	add_meta_box(
		'zz_projet_gallery',
		'Galerie',
		'zz_projet_gallery_render',
		'projet',
		'normal',
		'high'
	);
});

function zz_projet_metabox_render($post) {
	wp_nonce_field('zz_projet_save', 'zz_projet_nonce');

	$fields = [
		'_projet_client'       => ['label' => 'Client', 'type' => 'text'],
		'_projet_year'         => ['label' => 'Année', 'type' => 'text'],
		'_projet_location'     => ['label' => 'Lieu / Adresse', 'type' => 'text'],
		'_projet_instagram'    => ['label' => 'Instagram (@handle)', 'type' => 'text'],
		'_projet_website'      => ['label' => 'Site web (URL)', 'type' => 'text'],
		'_projet_nature'       => ['label' => 'Nature du projet', 'type' => 'richtext', 'rows' => 3],
		'_projet_materiaux'    => ['label' => 'Matériaux', 'type' => 'richtext', 'rows' => 3],
		'_projet_collaborateur' => ['label' => 'Collaborateur / Crédits', 'type' => 'richtext', 'rows' => 3],
		'_projet_short_desc'   => ['label' => 'Description courte (carte)', 'type' => 'text'],
		'_projet_description'  => ['label' => 'Description longue', 'type' => 'richtext', 'rows' => 8],
	];

	echo '<table class="form-table"><tbody>';
	foreach ($fields as $key => $field) {
		$value = get_post_meta($post->ID, $key, true);
		echo '<tr>';
		echo '<th><label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label></th>';
		if ($field['type'] === 'richtext') {
			$rows = isset($field['rows']) ? $field['rows'] : 4;
			echo '<td>';
			wp_editor($value, $key, [
				'textarea_name' => $key,
				'textarea_rows' => $rows,
				'media_buttons' => false,
				'teeny'         => false,
				'quicktags'     => false,
				'tinymce'       => [
					'toolbar1'          => 'bold,italic,link,unlink,bullist,numlist,removeformat',
					'toolbar2'          => '',
					'forced_root_block' => 'p',
					'valid_elements'    => 'p,br,strong/b,em/i,a[href|target|rel],ul,ol,li,span',
					'block_formats'     => 'Paragraph=p',
				],
			]);
			echo '</td>';
		} else {
			echo '<td><input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text"></td>';
		}
		echo '</tr>';
	}
	echo '</tbody></table>';
}

function zz_projet_gallery_render($post) {
	$gallery = get_post_meta($post->ID, '_projet_gallery', true);
	if (!is_array($gallery)) $gallery = [];
	$orientations = get_post_meta($post->ID, '_projet_gallery_orientations', true);
	if (!is_array($orientations)) $orientations = [];
	/* Migrer les anciennes valeurs "portrait" (defaut avant v2) vers "auto" */
	foreach ($orientations as $k => $v) {
		if ($v === 'portrait') $orientations[$k] = 'auto';
	}
	?>
	<p class="zz-pgal-hint">La première image sert de cover. Glissez-déposez pour réordonner. Cliquez sur ↻ pour changer l'orientation.</p>
	<div class="zz-pgal-grid" id="zz-pgal-grid">
		<?php foreach ($gallery as $i => $att_id) :
			$att_id = intval($att_id);
			$url = wp_get_attachment_url($att_id);
			if (!$url) continue;
			$mime = get_post_mime_type($att_id);
			$is_video = (strpos($mime, 'video/') === 0);
			if ($is_video) {
				$display_url = preg_replace('/\.(mp4|webm|mov)$/i', '-poster.jpg', $url);
			} else {
				$thumb = wp_get_attachment_image_src($att_id, 'medium_large');
				$display_url = $thumb ? $thumb[0] : $url;
			}
			$orient = isset($orientations[$att_id]) ? $orientations[$att_id] : 'auto';
		?>
		<div class="zz-pgal-card<?php echo $is_video ? ' zz-pgal-card--video' : ''; ?>" draggable="true" data-id="<?php echo $att_id; ?>" data-index="<?php echo $i; ?>" data-orientation="<?php echo esc_attr($orient); ?>">
			<img src="<?php echo esc_url($display_url); ?>" alt="" loading="lazy">
			<?php if ($is_video) : ?>
			<span class="zz-pgal-play"><svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></span>
			<?php endif; ?>
			<span class="zz-pgal-badge"><?php echo $i === 0 ? 'Cover' : ($i + 1); ?></span>
			<button type="button" class="zz-pgal-orient" title="Changer l'orientation"><svg viewBox="0 0 24 24"><path d="M1 4v6h6M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg></button>
			<button type="button" class="zz-pgal-remove" title="Retirer">&times;</button>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="zz-pgal-actions">
		<button type="button" class="button zz-pgal-add" id="zz-pgal-add">
			<span class="dashicons dashicons-plus-alt2" style="margin-top:3px"></span> Ajouter des médias
		</button>
	</div>
	<input type="hidden" name="_projet_gallery" id="zz-pgal-data" value="<?php echo esc_attr(wp_json_encode(array_map('intval', $gallery))); ?>">
	<input type="hidden" name="_projet_gallery_orientations" id="zz-pgal-orientations" value="<?php echo esc_attr(wp_json_encode($orientations)); ?>">

	<script>
	(function() {
		var grid = document.getElementById('zz-pgal-grid');
		var dataInput = document.getElementById('zz-pgal-data');
		var orientInput = document.getElementById('zz-pgal-orientations');
		if (!grid || !dataInput) return;

		var COLS = 3;
		var GAP = 10;
		var ORIENT_CYCLE = ['auto', 'portrait', 'landscape', 'square'];

		/* --- Masonry layout (round-robin : ordre DOM = ordre visuel) --- */
		function layoutMasonry() {
			var cards = grid.querySelectorAll('.zz-pgal-card');
			if (!cards.length) { grid.style.height = '60px'; return; }
			var gridW = grid.offsetWidth;
			if (gridW < 10) gridW = 560;
			var colW = (gridW - GAP * (COLS - 1)) / COLS;
			var colH = new Array(COLS).fill(0);

			for (var i = 0; i < cards.length; i++) {
				var card = cards[i];
				var img = card.querySelector('img');
				var orient = card.getAttribute('data-orientation');
				var cardH;
				if (orient === 'portrait') { cardH = colW * (4 / 3); }
				else if (orient === 'landscape') { cardH = colW * (3 / 4); }
				else if (orient === 'square') { cardH = colW; }
				else {
					var natW = img && img.naturalWidth ? img.naturalWidth : 1;
					var natH = img && img.naturalHeight ? img.naturalHeight : 1;
					cardH = colW * (natH / natW);
				}

				/* Round-robin : carte i va dans colonne i % COLS */
				var col = i % COLS;

				card.style.width = colW + 'px';
				card.style.height = cardH + 'px';
				card.style.left = col * (colW + GAP) + 'px';
				card.style.top = colH[col] + 'px';

				colH[col] += cardH + GAP;
			}

			var maxH = 0;
			for (var c = 0; c < COLS; c++) { if (colH[c] > maxH) maxH = colH[c]; }
			grid.style.height = (maxH - GAP) + 'px';
		}

		function waitImagesAndLayout() {
			var imgs = grid.querySelectorAll('img');
			var loaded = 0;
			var total = imgs.length;
			if (total === 0) { layoutMasonry(); return; }
			function check() { loaded++; if (loaded >= total) layoutMasonry(); }
			for (var i = 0; i < total; i++) {
				if (imgs[i].complete) { check(); }
				else { imgs[i].onload = check; imgs[i].onerror = check; }
			}
		}

		waitImagesAndLayout();
		/* Re-layout au cas ou la metabox n'etait pas visible au premier passage */
		setTimeout(layoutMasonry, 300);

		function syncData() {
			var cards = grid.querySelectorAll('.zz-pgal-card');
			var ids = [];
			var orients = {};
			for (var i = 0; i < cards.length; i++) {
				var id = parseInt(cards[i].getAttribute('data-id'), 10);
				ids.push(id);
				var o = cards[i].getAttribute('data-orientation') || 'auto';
				orients[id] = o;
			}
			dataInput.value = JSON.stringify(ids);
			if (orientInput) orientInput.value = JSON.stringify(orients);
			updateBadges();
		}

		function updateBadges() {
			var cards = grid.querySelectorAll('.zz-pgal-card');
			for (var i = 0; i < cards.length; i++) {
				var badge = cards[i].querySelector('.zz-pgal-badge');
				if (badge) {
					badge.textContent = i === 0 ? 'Cover' : (i + 1);
					badge.className = 'zz-pgal-badge' + (i === 0 ? ' zz-pgal-badge--cover' : '');
				}
				cards[i].setAttribute('data-index', i);
			}
		}

		function buildCard(attId, url, isVideo, idx) {
			var displayUrl = isVideo ? url.replace(/\.(mp4|webm|mov)$/i, '-poster.jpg') : url;
			var div = document.createElement('div');
			div.className = 'zz-pgal-card' + (isVideo ? ' zz-pgal-card--video' : '');
			div.draggable = true;
			div.setAttribute('data-id', attId);
			div.setAttribute('data-index', idx);
			div.setAttribute('data-orientation', 'auto');
			var badgeClass = idx === 0 ? 'zz-pgal-badge zz-pgal-badge--cover' : 'zz-pgal-badge';
			var badgeText = idx === 0 ? 'Cover' : (idx + 1);
			var playHtml = isVideo ? '<span class="zz-pgal-play"><svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21"/></svg></span>' : '';
			div.innerHTML = '<img src="' + displayUrl + '" alt="" loading="lazy">'
				+ playHtml
				+ '<span class="' + badgeClass + '">' + badgeText + '</span>'
				+ '<button type="button" class="zz-pgal-orient" title="Changer l\'orientation"><svg viewBox="0 0 24 24"><path d="M1 4v6h6M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg></button>'
				+ '<button type="button" class="zz-pgal-remove" title="Retirer">&times;</button>';
			return div;
		}

		/* --- Drag and drop : 3 zones (before / swap / after) --- */
		var dragSrc = null;
		var dropPos = null;  /* 'before', 'swap', ou 'after' */
		var dropTarget = null;

		function clearDropIndicators() {
			var all = grid.querySelectorAll('.zz-pgal-card');
			for (var i = 0; i < all.length; i++) {
				all[i].classList.remove('is-drag-over', 'is-drop-before', 'is-drop-after', 'is-drop-swap');
			}
		}

		/* Trouver la carte la plus proche du curseur (pour les zones vides du grid) */
		function findClosestCard(x, y) {
			var cards = grid.querySelectorAll('.zz-pgal-card');
			var best = null;
			var bestDist = Infinity;
			for (var i = 0; i < cards.length; i++) {
				if (cards[i] === dragSrc) continue;
				var r = cards[i].getBoundingClientRect();
				/* Distance du point au centre de la carte */
				var cx = r.left + r.width / 2;
				var cy = r.top + r.height / 2;
				var d = Math.abs(x - cx) + Math.abs(y - cy);
				if (d < bestDist) { bestDist = d; best = cards[i]; }
			}
			return best;
		}

		function detectDropZone(card, clientY) {
			var rect = card.getBoundingClientRect();
			var relY = clientY - rect.top;
			var edge = Math.min(30, rect.height * 0.3);
			if (relY < edge) return 'before';
			if (relY > rect.height - edge) return 'after';
			return 'swap';
		}

		grid.addEventListener('dragstart', function(e) {
			var card = e.target.closest('.zz-pgal-card');
			if (!card) return;
			dragSrc = card;
			dropPos = null;
			dropTarget = null;
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('text/plain', '');
			requestAnimationFrame(function() { card.classList.add('is-dragging'); });
		});

		grid.addEventListener('dragover', function(e) {
			e.preventDefault();
			e.dataTransfer.dropEffect = 'move';

			var card = e.target.closest('.zz-pgal-card');

			/* Si on est sur le grid vide, trouver la carte la plus proche */
			if (!card || card === dragSrc) {
				card = findClosestCard(e.clientX, e.clientY);
			}
			if (!card || card === dragSrc) return;

			/* Detecter la zone : curseur sur la carte ou a cote */
			var rect = card.getBoundingClientRect();
			var isOnCard = (e.clientY >= rect.top && e.clientY <= rect.bottom &&
							e.clientX >= rect.left && e.clientX <= rect.right);
			var pos;
			if (isOnCard) {
				pos = detectDropZone(card, e.clientY);
			} else {
				/* A cote / en-dessous : inserer after */
				pos = (e.clientY < rect.top + rect.height / 2) ? 'before' : 'after';
			}

			/* Eviter les re-renders inutiles */
			if (dropTarget === card && card.classList.contains('is-drop-' + pos)) return;

			clearDropIndicators();
			card.classList.add('is-drag-over', 'is-drop-' + pos);
			dropPos = pos;
			dropTarget = card;
		});

		grid.addEventListener('dragleave', function(e) {
			var card = e.target.closest('.zz-pgal-card');
			if (card && !card.contains(e.relatedTarget)) {
				card.classList.remove('is-drag-over', 'is-drop-before', 'is-drop-after', 'is-drop-swap');
			}
		});

		grid.addEventListener('drop', function(e) {
			e.preventDefault();
			var target = dropTarget || e.target.closest('.zz-pgal-card');
			if (!target || !dragSrc || target === dragSrc) { clearDropIndicators(); return; }

			clearDropIndicators();

			if (dropPos === 'swap') {
				var placeholder = document.createElement('div');
				grid.insertBefore(placeholder, dragSrc);
				grid.insertBefore(dragSrc, target);
				grid.insertBefore(target, placeholder);
				grid.removeChild(placeholder);
			} else if (dropPos === 'before') {
				grid.insertBefore(dragSrc, target);
			} else {
				grid.insertBefore(dragSrc, target.nextSibling);
			}
			syncData();
			layoutMasonry();
		});

		grid.addEventListener('dragend', function() {
			clearDropIndicators();
			var cards = grid.querySelectorAll('.zz-pgal-card');
			for (var i = 0; i < cards.length; i++) {
				cards[i].classList.remove('is-dragging');
			}
			dragSrc = null;
			dropPos = null;
			dropTarget = null;
		});

		/* --- Orientation toggle --- */
		grid.addEventListener('click', function(e) {
			var btn = e.target.closest('.zz-pgal-orient');
			if (!btn) return;
			e.preventDefault();
			e.stopPropagation();
			var card = btn.closest('.zz-pgal-card');
			if (!card) return;
			var current = card.getAttribute('data-orientation') || 'auto';
			var idx = ORIENT_CYCLE.indexOf(current);
			var next = ORIENT_CYCLE[(idx + 1) % ORIENT_CYCLE.length];
			card.setAttribute('data-orientation', next);
			syncData();
			layoutMasonry();
		});

		/* --- Remove --- */
		grid.addEventListener('click', function(e) {
			if (!e.target.classList.contains('zz-pgal-remove')) return;
			e.preventDefault();
			var card = e.target.closest('.zz-pgal-card');
			if (card) {
				card.remove();
				syncData();
				if (!grid.querySelector('.zz-pgal-card')) {
					grid.innerHTML = '<div class="zz-pgal-empty">Aucune image. Cliquez sur « Ajouter des médias » ci-dessous.</div>';
					grid.style.height = '60px';
				} else {
					layoutMasonry();
				}
			}
		});

		/* --- Add via wp.media --- */
		document.getElementById('zz-pgal-add').addEventListener('click', function(e) {
			e.preventDefault();
			var frame = wp.media({
				title: 'Ajouter des médias (images & vidéos)',
				button: { text: 'Ajouter' },
				multiple: true,
				library: { type: ['image', 'video'] }
			});
			frame.on('select', function() {
				var empty = grid.querySelector('.zz-pgal-empty');
				if (empty) empty.remove();

				var selection = frame.state().get('selection');
				selection.each(function(attachment) {
					var id = attachment.id;
					var url = attachment.attributes.url;
					var isVideo = (attachment.attributes.type === 'video');
					var idx = grid.querySelectorAll('.zz-pgal-card').length;
					grid.appendChild(buildCard(id, url, isVideo, idx));
				});
				syncData();
				waitImagesAndLayout();
			});
			frame.open();
		});
	})();
	</script>
	<?php
}

/**
 * Sauvegarder les meta fields projet
 */
add_action('save_post_projet', function ($post_id) {
	if (!isset($_POST['zz_projet_nonce']) || !wp_verify_nonce($_POST['zz_projet_nonce'], 'zz_projet_save')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	$text_fields = ['_projet_client', '_projet_year', '_projet_location', '_projet_short_desc', '_projet_instagram', '_projet_website'];
	foreach ($text_fields as $key) {
		if (isset($_POST[$key])) {
			update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
		}
	}

	$richtext_fields = ['_projet_description', '_projet_nature', '_projet_materiaux', '_projet_collaborateur'];
	foreach ($richtext_fields as $key) {
		if (isset($_POST[$key])) {
			update_post_meta($post_id, $key, wp_kses_post($_POST[$key]));
		}
	}

	if (isset($_POST['_projet_gallery']) && is_string($_POST['_projet_gallery'])) {
		$raw = sanitize_text_field(wp_unslash($_POST['_projet_gallery']));
		$decoded = json_decode($raw, true);
		if (is_array($decoded)) {
			$gallery = array_values(array_filter(array_map('intval', $decoded)));
		} else {
			$gallery = [];
		}
		update_post_meta($post_id, '_projet_gallery', $gallery);

		// Mettre à jour la thumbnail depuis la cover (1er item image)
		if (!empty($gallery)) {
			$thumb_id = null;
			foreach ($gallery as $att_id) {
				$mime = get_post_mime_type($att_id);
				if (strpos($mime, 'image/') === 0) {
					$thumb_id = $att_id;
					break;
				}
			}
			if ($thumb_id) {
				set_post_thumbnail($post_id, $thumb_id);
			}
		}

		// Auto-traitement vidéo : générer les posters manquants
		zz_process_gallery_videos($gallery);
	}

	// Sauvegarder les orientations de la galerie
	if (isset($_POST['_projet_gallery_orientations']) && is_string($_POST['_projet_gallery_orientations'])) {
		$raw_orient = wp_unslash($_POST['_projet_gallery_orientations']);
		$decoded_orient = json_decode($raw_orient, true);
		if (is_array($decoded_orient)) {
			$allowed = ['auto', 'portrait', 'landscape', 'square'];
			$clean = [];
			foreach ($decoded_orient as $id => $orient) {
				if (in_array($orient, $allowed, true)) {
					$clean[intval($id)] = $orient;
				}
			}
			update_post_meta($post_id, '_projet_gallery_orientations', $clean);
		}
	}
});

/**
 * Traiter les vidéos de la galerie : générer poster + compresser si nécessaire
 */
function zz_process_gallery_videos($gallery) {
	$paths = [
		'/home/zougzoug/.local/bin/ffmpeg',
		'/usr/bin/ffmpeg',
		'/usr/local/bin/ffmpeg',
	];
	$ffmpeg = '';
	foreach ($paths as $path) {
		if (is_executable($path)) {
			$ffmpeg = $path;
			break;
		}
	}
	if (empty($ffmpeg)) return;

	foreach ($gallery as $att_id) {
		$att_id = intval($att_id);
		$mime = get_post_mime_type($att_id);
		if (strpos($mime, 'video/') !== 0) continue;

		$abs_video = get_attached_file($att_id);
		if (!$abs_video || !file_exists($abs_video)) continue;

		// Poster
		$poster_path = preg_replace('/\.(mp4|webm|mov)$/i', '-poster.jpg', $abs_video);
		if (!file_exists($poster_path)) {
			$cmd = sprintf(
				'%s -i %s -ss 00:00:01 -vframes 1 -q:v 2 %s 2>&1',
				escapeshellarg($ffmpeg),
				escapeshellarg($abs_video),
				escapeshellarg($poster_path)
			);
			shell_exec($cmd);

			// Fallback frame 0
			if (!file_exists($poster_path)) {
				$cmd2 = sprintf(
					'%s -i %s -ss 00:00:00 -vframes 1 -q:v 2 %s 2>&1',
					escapeshellarg($ffmpeg),
					escapeshellarg($abs_video),
					escapeshellarg($poster_path)
				);
				shell_exec($cmd2);
			}
		}
	}
}

/**
 * Admin : colonnes custom dans la liste des projets
 */
add_filter('manage_projet_posts_columns', function ($columns) {
	$new = [];
	$new['cb'] = $columns['cb'];
	$new['zz_thumb'] = '';
	$new['title'] = $columns['title'];
	$new['zz_client'] = 'Client';
	$new['taxonomy-categorie_projet'] = 'Catégorie';
	$new['zz_year'] = 'Année';
	$new['zz_desc'] = 'Description';
	$new['date'] = $columns['date'];
	return $new;
});

add_action('manage_projet_posts_custom_column', function ($column, $post_id) {
	switch ($column) {
		case 'zz_thumb':
			$thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
			if (!$thumb) {
				$gallery = get_post_meta($post_id, '_projet_gallery', true);
				if (is_array($gallery) && !empty($gallery)) {
					$thumb = wp_get_attachment_image_url(intval($gallery[0]), 'thumbnail');
				}
			}
			if ($thumb) {
				echo '<img src="' . esc_url($thumb) . '" alt="" class="zz-projet-list-thumb">';
			} else {
				echo '<span class="zz-projet-list-nothumb dashicons dashicons-format-image"></span>';
			}
			break;
		case 'zz_client':
			echo esc_html(get_post_meta($post_id, '_projet_client', true));
			break;
		case 'zz_year':
			echo esc_html(get_post_meta($post_id, '_projet_year', true));
			break;
		case 'zz_desc':
			$desc = get_post_meta($post_id, '_projet_short_desc', true);
			if ($desc) {
				echo '<span class="zz-projet-list-desc">' . esc_html($desc) . '</span>';
			}
			break;
	}
}, 10, 2);

add_filter('manage_edit-projet_sortable_columns', function ($columns) {
	$columns['zz_client'] = '_projet_client';
	$columns['zz_year'] = '_projet_year';
	return $columns;
});

add_action('pre_get_posts', function ($query) {
	if (!is_admin() || !$query->is_main_query()) return;
	if ($query->get('post_type') !== 'projet') return;
	$orderby = $query->get('orderby');
	if ($orderby === '_projet_client') {
		$query->set('meta_key', '_projet_client');
		$query->set('orderby', 'meta_value');
	} elseif ($orderby === '_projet_year') {
		$query->set('meta_key', '_projet_year');
		$query->set('orderby', 'meta_value_num');
	} elseif (empty($orderby) || $orderby === 'menu_order title') {
		// Default: order by menu_order
		$query->set('orderby', 'menu_order');
		$query->set('order', 'ASC');
	}
});

/**
 * Admin : styles custom pour la liste des projets
 */
add_action('admin_head', function () {
	$screen = get_current_screen();
	if (!$screen || $screen->id !== 'edit-projet') return;
	?>
	<style>
		/* ===== Page wrapper ===== */
		.post-type-projet .wrap {
			max-width: 1400px;
			margin: 0 auto;
			padding: 20px 20px 40px;
		}

		/* ===== Hero — titre + bouton ===== */
		.post-type-projet .wrap > h1.wp-heading-inline {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 600;
			font-size: 26px;
			color: #1A1A1A;
			letter-spacing: -0.01em;
		}
		.post-type-projet .wrap > .page-title-action {
			background: #1A1A1A;
			color: #fff;
			border: none;
			border-radius: 6px;
			padding: 6px 16px;
			font-size: 13px;
			font-weight: 500;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			text-decoration: none;
			transition: background 0.15s;
		}
		.post-type-projet .wrap > .page-title-action:hover {
			background: #333;
			color: #fff;
		}

		/* ===== Sous-sous links (Tous | Publiés) ===== */
		.post-type-projet .subsubsub {
			font-size: 13px;
			margin: 8px 0 0;
		}
		.post-type-projet .subsubsub a {
			color: #646970;
			text-decoration: none;
			font-weight: 400;
		}
		.post-type-projet .subsubsub a:hover,
		.post-type-projet .subsubsub .current a {
			color: #1A1A1A;
			font-weight: 500;
		}
		.post-type-projet .subsubsub .count {
			color: #a0a5aa;
		}

		/* ===== Toolbar filtres ===== */
		.post-type-projet .tablenav.top {
			display: flex;
			align-items: center;
			gap: 8px;
			padding: 12px 0;
			border-bottom: 1px solid rgba(26,26,26,0.08);
			margin-bottom: 0;
		}
		.post-type-projet .tablenav .actions {
			display: flex;
			align-items: center;
			gap: 6px;
			padding: 0;
		}
		.post-type-projet .tablenav .actions select {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 8px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #1A1A1A;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.post-type-projet .tablenav .actions .button {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 12px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #646970;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			cursor: pointer;
			transition: border-color 0.15s, color 0.15s;
		}
		.post-type-projet .tablenav .actions .button:hover {
			border-color: #1A1A1A;
			color: #1A1A1A;
		}

		/* ===== Search box ===== */
		.post-type-projet .search-box {
			display: flex;
			align-items: center;
			gap: 6px;
			margin-left: auto !important;
		}
		.post-type-projet .search-box input[type="search"] {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 10px;
			font-size: 13px;
			min-height: 32px;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.post-type-projet .search-box input[type="search"]:focus {
			border-color: #1A1A1A;
			box-shadow: 0 0 0 1px #1A1A1A;
			outline: none;
		}
		.post-type-projet .search-box .button {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 12px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #646970;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			cursor: pointer;
			transition: border-color 0.15s, color 0.15s;
		}
		.post-type-projet .search-box .button:hover {
			border-color: #1A1A1A;
			color: #1A1A1A;
		}

		/* ===== Compteur elements ===== */
		.post-type-projet .tablenav .displaying-num {
			font-size: 12px;
			color: #a0a5aa;
			font-style: normal;
		}

		/* ===== Table ===== */
		.post-type-projet .wp-list-table {
			border-collapse: separate;
			border-spacing: 0;
			border: none;
		}
		.post-type-projet .wp-list-table thead th,
		.post-type-projet .wp-list-table tfoot th {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 500;
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			color: #a0a5aa;
			border-bottom: 1px solid rgba(26,26,26,0.08);
			padding: 10px 10px;
			background: transparent;
		}
		.post-type-projet .wp-list-table thead th a,
		.post-type-projet .wp-list-table tfoot th a {
			color: #a0a5aa;
			text-decoration: none;
		}
		.post-type-projet .wp-list-table thead th a:hover,
		.post-type-projet .wp-list-table tfoot th a:hover {
			color: #1A1A1A;
		}
		.post-type-projet .wp-list-table thead th.sorted a {
			color: #1A1A1A;
		}

		/* ===== Rows ===== */
		.post-type-projet .wp-list-table tbody tr td {
			vertical-align: middle;
			padding: 10px;
			border-bottom: 1px solid rgba(26,26,26,0.05);
			background: transparent;
		}
		.post-type-projet .wp-list-table tbody tr:hover td {
			background: rgba(26,26,26,0.015);
		}
		.post-type-projet .wp-list-table tbody tr .column-title strong a {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 500;
			font-size: 14px;
			color: #1A1A1A;
			text-decoration: none;
		}
		.post-type-projet .wp-list-table tbody tr .column-title strong a:hover {
			color: #555;
		}
		.post-type-projet .wp-list-table tbody tr .row-actions a {
			color: #646970;
			font-size: 12px;
		}

		/* ===== Thumbnail column ===== */
		.column-zz_thumb { width: 60px; }
		.zz-projet-list-thumb {
			width: 50px;
			height: 50px;
			object-fit: cover;
			border-radius: 6px;
			display: block;
		}
		.zz-projet-list-nothumb {
			width: 50px;
			height: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #f5f5f5;
			border-radius: 6px;
			color: #ccc;
			font-size: 20px;
		}

		/* ===== Description column ===== */
		.zz-projet-list-desc {
			color: #646970;
			font-size: 13px;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}

		/* ===== Compact columns ===== */
		.column-zz_year { width: 70px; }
		.column-zz_client { width: 140px; }

		/* ===== Bottom tablenav clean ===== */
		.post-type-projet .tablenav.bottom {
			border-top: 1px solid rgba(26,26,26,0.08);
			margin-top: 0;
			padding-top: 12px;
		}

		/* ===== Hide border-bottom on table wrapper ===== */
		.post-type-projet .wp-list-table,
		.post-type-projet .wp-list-table tr:last-child td {
			border-bottom: none;
		}
	</style>
	<?php
});

/**
 * Admin : enqueue media sur l'écran d'édition projet
 */
add_action('admin_enqueue_scripts', function ($hook) {
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'projet' || $screen->base !== 'post') return;
	wp_enqueue_media();
});

/**
 * Admin : styles galerie projet via admin_head
 */
add_action('admin_head', function () {
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'projet' || $screen->base !== 'post') return;
	?>
	<style>
		.zz-pgal-hint { color: #646970; font-size: 13px; margin: 0 0 12px; }
		.zz-pgal-grid {
			position: relative;
			max-width: 560px;
			min-height: 60px;
		}
		.zz-pgal-card {
			position: absolute;
			border-radius: 4px;
			overflow: hidden;
			cursor: grab;
			transition: opacity 0.15s, box-shadow 0.15s;
			background: #f0f0f0;
		}
		.zz-pgal-card:active { cursor: grabbing; }
		.zz-pgal-card.is-dragging { opacity: 0.25; z-index: 0; }
		.zz-pgal-card.is-drag-over { z-index: 2; overflow: visible; }
		/* Swap : ring accent autour */
		.zz-pgal-card.is-drop-swap { outline: 3px solid #B8956A; outline-offset: -1px; }
		/* Insert before/after : barre visible hors carte */
		.zz-pgal-card.is-drop-before::before,
		.zz-pgal-card.is-drop-after::after {
			content: '';
			position: absolute;
			left: -4px; right: -4px;
			height: 4px;
			background: #B8956A;
			border-radius: 3px;
			z-index: 10;
			box-shadow: 0 0 8px rgba(184,149,106,0.5);
			pointer-events: none;
		}
		.zz-pgal-card.is-drop-before::before { top: -5px; }
		.zz-pgal-card.is-drop-after::after { bottom: -5px; }
		.zz-pgal-card img { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; }
		.zz-pgal-badge {
			position: absolute; top: 4px; left: 4px;
			background: rgba(26,26,26,0.7); color: #fff;
			font-size: 9px; font-weight: 600; padding: 2px 5px;
			border-radius: 3px; line-height: 1.3;
			letter-spacing: 0.03em; text-transform: uppercase;
		}
		.zz-pgal-badge--cover { background: #1A1A1A; }
		.zz-pgal-remove {
			position: absolute; top: 3px; right: 3px;
			width: 20px; height: 20px; border: none; border-radius: 50%;
			background: rgba(0,0,0,0.6); color: #fff;
			font-size: 13px; line-height: 20px; text-align: center;
			cursor: pointer; opacity: 0; transition: opacity 0.15s; padding: 0;
		}
		.zz-pgal-card:hover .zz-pgal-remove { opacity: 1; }
		.zz-pgal-remove:hover { background: #c00; }
		/* Bouton rotation orientation */
		.zz-pgal-orient {
			position: absolute; bottom: 3px; right: 3px;
			width: 20px; height: 20px; border: none; border-radius: 50%;
			background: rgba(0,0,0,0.5); color: #fff;
			font-size: 11px; line-height: 20px; text-align: center;
			cursor: pointer; opacity: 0; transition: opacity 0.15s; padding: 0;
			display: flex; align-items: center; justify-content: center;
		}
		.zz-pgal-card:hover .zz-pgal-orient { opacity: 1; }
		.zz-pgal-orient:hover { background: rgba(184,149,106,0.9); }
		.zz-pgal-orient svg { width: 12px; height: 12px; fill: none; stroke: #fff; stroke-width: 2; }
		.zz-pgal-actions { margin-top: 12px; display: flex; gap: 8px; }
		.zz-pgal-actions .button .dashicons { vertical-align: middle; }
		.zz-pgal-empty {
			text-align: center; padding: 40px 20px;
			color: #b0b0b0; font-size: 13px; border: 2px dashed #ddd; border-radius: 6px;
		}
		/* Video card */
		.zz-pgal-card--video { border: 2px solid rgba(26,26,26,0.15); }
		.zz-pgal-play {
			position: absolute; top: 50%; left: 50%;
			transform: translate(-50%, -50%);
			width: 28px; height: 28px;
			background: rgba(0,0,0,0.6); border-radius: 50%;
			display: flex; align-items: center; justify-content: center;
			pointer-events: none;
		}
		.zz-pgal-play svg { width: 12px; height: 12px; fill: #fff; margin-left: 2px; stroke: none; }
	</style>
	<?php
});

/**
 * Drag-and-drop reorder sur la liste des projets
 */
add_action('admin_enqueue_scripts', function ($hook) {
	$screen = get_current_screen();
	if (!$screen || $screen->id !== 'edit-projet') return;
	wp_enqueue_script('jquery-ui-sortable');
});

add_action('admin_head', function () {
	$screen = get_current_screen();
	if (!$screen || $screen->id !== 'edit-projet') return;
	?>
	<style>
		.post-type-projet .wp-list-table tbody tr { cursor: grab; }
		.post-type-projet .wp-list-table tbody tr:active { cursor: grabbing; }
		.post-type-projet .wp-list-table tbody tr.zz-sortable-placeholder {
			background: #f0f6fc;
			border: 2px dashed #2271b1;
			height: 50px;
		}
		.post-type-projet .wp-list-table tbody tr.zz-sortable-placeholder td { border: none; }
		.post-type-projet .wp-list-table tbody tr.ui-sortable-helper {
			background: #fff;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			display: table;
		}
		.zz-sort-notice {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 12px;
			background: #e7f5e7;
			color: #1e7e1e;
			font-size: 13px;
			border-radius: 4px;
			margin-left: 12px;
			opacity: 0;
			transition: opacity 0.3s;
		}
		.zz-sort-notice.is-visible { opacity: 1; }
		.zz-sort-notice .dashicons { font-size: 16px; width: 16px; height: 16px; }
	</style>
	<script>
	jQuery(function($) {
		var $tbody = $('.post-type-projet .wp-list-table tbody#the-list');
		if (!$tbody.length) return;

		// Add sort notice
		$('.subsubsub').after('<span class="zz-sort-notice" id="zz-sort-notice"><span class="dashicons dashicons-yes-alt"></span> Ordre sauvegardé</span>');

		$tbody.sortable({
			handle: 'td',
			placeholder: 'zz-sortable-placeholder',
			axis: 'y',
			helper: function(e, row) {
				// Preserve column widths during drag
				var cells = row.children();
				var helper = row.clone();
				helper.children().each(function(i) {
					$(this).width(cells.eq(i).width());
				});
				return helper;
			},
			update: function() {
				var order = [];
				$tbody.find('tr').each(function() {
					var id = $(this).attr('id');
					if (id) order.push(id.replace('post-', ''));
				});
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'zz_sort_projets',
						nonce: <?php echo wp_json_encode(wp_create_nonce('zz_sort_projets')); ?>,
						order: order
					},
					success: function() {
						var $notice = $('#zz-sort-notice');
						$notice.addClass('is-visible');
						setTimeout(function() { $notice.removeClass('is-visible'); }, 2000);
					}
				});
			}
		});
	});
	</script>
	<?php
});

add_action('wp_ajax_zz_sort_projets', function () {
	check_ajax_referer('zz_sort_projets', 'nonce');
	if (!current_user_can('edit_posts')) wp_send_json_error();

	$order = isset($_POST['order']) ? array_map('intval', $_POST['order']) : [];
	foreach ($order as $position => $post_id) {
		wp_update_post([
			'ID'         => $post_id,
			'menu_order' => $position,
		]);
	}
	wp_send_json_success();
});

/**
 * Preparer les donnees projets pour le front (wp_localize_script)
 */
function zz_get_projets_data() {
	// Category slug → filter value mapping
	$cat_filter_map = [
		'art-de-la-table' => 'table',
		'luminaires'      => 'luminaires',
		'accessoires'     => 'accessoires',
	];

	$query = new WP_Query([
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]);

	$projets = [];

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$id = get_the_ID();

			// Category
			$terms = wp_get_post_terms($id, 'categorie_projet');
			$cat_slug = !empty($terms) ? $terms[0]->slug : '';
			$cat_label = !empty($terms) ? $terms[0]->name : '';
			$cat_filter = isset($cat_filter_map[$cat_slug]) ? $cat_filter_map[$cat_slug] : $cat_slug;

			// Gallery — resolve attachment IDs to full URLs + orientations
			$gallery_ids = get_post_meta($id, '_projet_gallery', true);
			if (!is_array($gallery_ids)) $gallery_ids = [];
			$raw_orients = get_post_meta($id, '_projet_gallery_orientations', true);
			if (!is_array($raw_orients)) $raw_orients = [];

			$medias = [];
			$orientations = [];
			$cover  = '';
			foreach ($gallery_ids as $att_id) {
				$att_id = intval($att_id);
				$url = wp_get_attachment_url($att_id);
				if ($url) {
					$medias[] = $url;
					$o = isset($raw_orients[$att_id]) ? $raw_orients[$att_id] : 'auto';
					if ($o === 'portrait') $o = 'auto'; // migration anciennes valeurs
					$orientations[] = $o;
					if (empty($cover)) $cover = $url;
				}
			}

			// Details
			$details = [
				'client'        => get_post_meta($id, '_projet_client', true),
				'year'          => get_post_meta($id, '_projet_year', true),
				'location'      => get_post_meta($id, '_projet_location', true),
				'texte'         => wp_kses_post(get_post_meta($id, '_projet_description', true)),
				'nature'        => wp_kses_post(get_post_meta($id, '_projet_nature', true)),
				'materiaux'     => wp_kses_post(get_post_meta($id, '_projet_materiaux', true)),
				'instagram'     => get_post_meta($id, '_projet_instagram', true),
				'website'       => get_post_meta($id, '_projet_website', true),
				'collaborateur' => wp_kses_post(get_post_meta($id, '_projet_collaborateur', true)),
			];

			$projets[] = [
				'id'       => $id,
				'name'     => get_the_title(),
				'category' => $cat_filter,
				'catLabel' => $cat_label,
				'desc'     => get_post_meta($id, '_projet_short_desc', true),
				'cover'    => $cover,
				'medias'   => $medias,
				'orientations' => $orientations,
				'details'  => $details,
			];
		}
		wp_reset_postdata();
	}

	return [
		'projets' => $projets,
	];
}
