<?php
/**
 * Dashboard full custom — Remplace le dashboard WP par défaut
 */

/**
 * Supprimer tous les widgets dashboard par défaut
 */
add_action('wp_dashboard_setup', function () {
	global $wp_meta_boxes;
	$wp_meta_boxes['dashboard'] = [];

	// Ajouter notre widget custom
	wp_add_dashboard_widget(
		'zz_dashboard',
		'Atelier ZougZoug',
		'zz_dashboard_render'
	);
});

/**
 * Enqueue dashboard assets
 */
add_action('admin_enqueue_scripts', function ($hook) {
	if ($hook !== 'index.php') return;

	$uri = get_template_directory_uri();
	wp_enqueue_style('zz-dashboard', $uri . '/inc/admin/admin-dashboard.css', ['zz-admin-global'], ZZ_VERSION);
	wp_enqueue_script('zz-dashboard', $uri . '/inc/admin/admin-dashboard.js', [], ZZ_VERSION, true);
});

/**
 * Render du dashboard
 */
function zz_dashboard_render() {
	$user = wp_get_current_user();
	$prenom = $user->first_name ?: $user->display_name;
	$today = date_i18n('l j F Y');

	// Prochains événements
	$events = new WP_Query([
		'post_type'      => 'evenement',
		'posts_per_page' => 4,
		'meta_key'       => '_event_date_start',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [
			[
				'key'     => '_event_date_start',
				'value'   => date('Y-m-d'),
				'compare' => '>=',
				'type'    => 'DATE',
			],
		],
	]);

	// Derniers projets
	$projets = new WP_Query([
		'post_type'      => 'projet',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]);

	?>
	<div class="zz-dash">

		<!-- Header -->
		<div class="zz-dash-header">
			<div class="zz-dash-header-left">
				<svg class="zz-dash-logo" width="40" height="38" viewBox="0 0 50 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M10.0033 23.6486C10.6751 23.0327 11.4176 22.4925 12.2082 22.0422L12.2102 22.0351C17.133 19.2261 23 14.2279 23 6.36224V0H1.54637V6.17241H16.7397V6.36325C16.7397 10.3173 14.1601 13.7938 9.0724 16.6957L9.07035 16.7028C7.87626 17.3843 6.75694 18.1992 5.74309 19.1281C1.77167 22.7661 0 26.8636 0 32.414V48H18.5176C18.5176 48 21.3492 45.3525 21.3492 41.042C21.3492 37.0284 14.795 33.6903 15.4248 29.9392C15.8918 27.1604 20.1489 27.2624 22.2575 27.4179V21.3687C16.0516 20.3569 10.9854 23.1498 10.5092 28.9809C9.97974 35.4623 16.0157 38.5975 17.3173 40.4675C17.4566 40.6826 17.5058 40.7936 17.5058 41.0754C17.5058 41.4247 17.1371 41.8276 16.666 41.8276H6.26025V32.414C6.26025 27.9854 7.75644 25.7074 10.0033 23.6496V23.6486Z" fill="#1A1A1A"/>
					<path d="M36.1645 37.0448C34.4271 37.0448 32.7813 37.4365 31.3125 38.1343V30.9412C31.3125 25.4939 34.5836 22.4264 37.2123 20.3151C44.8969 14.1418 50 11.4419 50 4.69106C50 1.86491 48.1514 0.0171662 45.3528 0.0171662C39.5076 0.0181759 39.1659 8.68037 35.3215 8.68037C31.4772 8.68037 32.84 0 32.84 0C32.84 0 27.2829 0 25.949 0C25.949 0 23.7896 12.6788 32.6681 13.2927C41.8801 13.9298 43.0339 4.17106 45.2036 4.17106C45.8098 4.17106 46.061 4.49416 46.061 5.15854C46.061 9.91624 38.6904 12.914 33.1354 17.1588C29.0368 20.2909 25.0226 23.7228 25.0226 31.7297V47.3326C25.0093 47.5537 25 47.7748 25 47.999H25.0226H31.2919H31.3146V47.5446C31.5493 45.1203 33.6346 43.2181 36.1666 43.2181C38.854 43.2181 41.0412 45.3627 41.0412 48H47.3332C47.3332 41.96 42.3247 37.0468 36.1676 37.0468L36.1645 37.0448Z" fill="#1A1A1A"/>
				</svg>
				<div>
					<h2 class="zz-dash-greeting">Bonjour <?php echo esc_html($prenom); ?></h2>
					<p class="zz-dash-date"><?php echo esc_html($today); ?></p>
				</div>
			</div>
			<a href="<?php echo esc_url(home_url('/')); ?>" class="zz-dash-view-site" target="_blank">
				Voir le site
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
				</svg>
			</a>
		</div>

		<!-- Raccourcis -->
		<div class="zz-dash-shortcuts">
			<a href="<?php echo esc_url(admin_url('post-new.php?post_type=projet')); ?>" class="zz-dash-shortcut">
				<span class="zz-dash-shortcut-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
				</span>
				<span class="zz-dash-shortcut-label">Nouveau projet</span>
			</a>
			<a href="<?php echo esc_url(admin_url('post-new.php?post_type=evenement')); ?>" class="zz-dash-shortcut">
				<span class="zz-dash-shortcut-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
				</span>
				<span class="zz-dash-shortcut-label">Nouvel événement</span>
			</a>
			<a href="<?php echo esc_url(admin_url('admin.php?page=zz-contenu')); ?>" class="zz-dash-shortcut">
				<span class="zz-dash-shortcut-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
				</span>
				<span class="zz-dash-shortcut-label">Modifier les pages</span>
			</a>
			<a href="<?php echo esc_url(admin_url('upload.php')); ?>" class="zz-dash-shortcut">
				<span class="zz-dash-shortcut-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
				</span>
				<span class="zz-dash-shortcut-label">Mediatheque</span>
			</a>
		</div>

		<!-- Deux colonnes : Événements + Projets -->
		<div class="zz-dash-grid">

			<!-- Prochains événements -->
			<div class="zz-dash-card">
				<div class="zz-dash-card-header">
					<h3 class="zz-dash-card-title">Prochains événements</h3>
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=evenement')); ?>" class="zz-dash-card-link">Voir tout</a>
				</div>
				<?php if ($events->have_posts()) : ?>
				<div class="zz-dash-events">
					<?php while ($events->have_posts()) : $events->the_post();
						$start = get_post_meta(get_the_ID(), '_event_date_start', true);
						$lieu  = get_post_meta(get_the_ID(), '_event_lieu', true);
						$ville = get_post_meta(get_the_ID(), '_event_ville', true);
						$days_until = '';
						if ($start) {
							$diff = (int) ((strtotime($start) - time()) / 86400);
							if ($diff === 0) $days_until = "Aujourd'hui";
							elseif ($diff === 1) $days_until = 'Demain';
							elseif ($diff > 0) $days_until = 'Dans ' . $diff . ' jours';
						}
					?>
					<a href="<?php echo esc_url(get_edit_post_link()); ?>" class="zz-dash-event">
						<div class="zz-dash-event-date">
							<?php if ($start) : ?>
							<span class="zz-dash-event-day"><?php echo esc_html(date_i18n('j', strtotime($start))); ?></span>
							<span class="zz-dash-event-month"><?php echo esc_html(date_i18n('M', strtotime($start))); ?></span>
							<?php endif; ?>
						</div>
						<div class="zz-dash-event-info">
							<span class="zz-dash-event-name"><?php the_title(); ?></span>
							<span class="zz-dash-event-lieu"><?php echo esc_html($lieu ?: $ville); ?></span>
						</div>
						<?php if ($days_until) : ?>
						<span class="zz-dash-event-badge"><?php echo esc_html($days_until); ?></span>
						<?php endif; ?>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<?php else : ?>
				<p class="zz-dash-empty">Aucun événement à venir.<br><a href="<?php echo esc_url(admin_url('post-new.php?post_type=evenement')); ?>">Ajouter un événement</a></p>
				<?php endif; ?>
			</div>

			<!-- Derniers projets -->
			<div class="zz-dash-card">
				<div class="zz-dash-card-header">
					<h3 class="zz-dash-card-title">Derniers projets</h3>
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=projet')); ?>" class="zz-dash-card-link">Voir tout</a>
				</div>
				<?php if ($projets->have_posts()) : ?>
				<div class="zz-dash-projets">
					<?php while ($projets->have_posts()) : $projets->the_post();
						$thumb = get_the_post_thumbnail_url(get_the_ID(), 'projet-card');
					?>
					<a href="<?php echo esc_url(get_edit_post_link()); ?>" class="zz-dash-projet">
						<div class="zz-dash-projet-thumb">
							<?php if ($thumb) : ?>
							<img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>">
							<?php else : ?>
							<div class="zz-dash-projet-placeholder">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
							</div>
							<?php endif; ?>
						</div>
						<span class="zz-dash-projet-name"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<?php else : ?>
				<p class="zz-dash-empty">Aucun projet pour le moment.<br><a href="<?php echo esc_url(admin_url('post-new.php?post_type=projet')); ?>">Ajouter un projet</a></p>
				<?php endif; ?>
			</div>

		</div>

	</div>
	<?php
}
